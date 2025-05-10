<?php

namespace App\Livewire;

use App\Models\Store;
use App\Models\Package;
use App\Models\Subscription;
use App\Stripe\StripeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class SelectPackage extends Component
{
    public $packages = [];
    public $selectedPackageId = null;
    public $subscription = null;
    public $nextPackageId = null;
    public $store = null;
    public $id;

    public function mount($id = null)
    {
        $this->packages = Package::all();
        $this->id = $id;
        $this->subscription = Store::query()
            ->where('store_id', $id)
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'active');
            })
            ->first()
            ->subscription ?? null;

        $this->store = Store::query()->where('store_id', $id)->first() ?? null;

        $this->selectedPackageId = $this->subscription->package_id ?? null;

        $this->nextPackageId = $this->store?->billingInfo->next_package_id ?? null;
    }

    public function selectSubscription($packageId)
    {
        $user = auth()->user();
        $store = Store::query()->where('store_id', $this->id)->first();
        $currentSubscription = $store->subscription;
        $package = Package::find($packageId);

        $this->authorize('selectSubscription', [Store::class, $currentSubscription]);

        if (!$package) {
            session()->flash('error', 'A kiválasztott csomag nem érvényes.');
            return;
        }

        try {
            if ($currentSubscription) {
                $stripeSubscription = StripeService::cancelSubscription($currentSubscription->stripe_subscription_id, false);

                if ($stripeSubscription->current_period_end > time()) {
                    $schedule = StripeService::createSubscriptionSchedule($user->stripe_customer_id, $package, $stripeSubscription->current_period_end);

                    $store->billingInfo->update([
                        'next_package_id' => $packageId,
                        'subscription_schedule_id' => $schedule->id,
                    ]);

                    session()->flash('message', 'Az új csomag a jelenlegi előfizetés lejárta után lép életbe.');
                    return;
                }
            }

            $this->createNewSubscription($user, $package);
            session()->flash('message', 'Az előfizetési csomag sikeresen létrejött!');
        } catch (\Exception $e) {
            Log::error('Hiba az előfizetés kiválasztása közben: ' . $e->getMessage());
            session()->flash('error', 'Hiba történt az előfizetés kiválasztása közben: ' . $e->getMessage());
        }
    }

    private function createNewSubscription($user, $package)
    {
        try {
            $stripeSubscription = StripeService::createSubscription($user, $package, 'monthly', 0);

            Subscription::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'status' => 'active',
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Hiba az új előfizetés létrehozása közben: ' . $e->getMessage());
            throw $e;
        }
    }

    public function cancelSubscription($nextSubscription)
    {
        $this->authorize('cancelSubscription', [Store::class, $this->subscription]);

        DB::beginTransaction();

        try {
            if (!$this->subscription) {
                session()->flash('error', 'Nincs aktív előfizetés.');
                return;
            }

            if ($nextSubscription === 0) {
                StripeService::cancelSubscription($this->subscription->stripe_subscription_id);

                $this->subscription->update([
                    'auto_renewal' => false,
                ]);
            } else if ($nextSubscription === 1) {
                StripeService::cancelSubscriptionSchedule($this->store->billingInfo->subscription_schedule_id);

                $this->store->billingInfo->update([
                    'subscription_schedule_id' => null,
                    'next_package_id' => null,
                ]);
            }

            DB::commit();
            session()->flash('message', 'Az előfizetés sikeresen lemondva.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hiba az előfizetés lemondása közben: ' . $e->getMessage());
            session()->flash('error', 'Hiba történt az előfizetés lemondása közben: ' . $e->getMessage());
        }
    }

    public function reactivateSubscription()
    {
        DB::beginTransaction();

        try {
            if (!$this->subscription || $this->subscription->auto_renewal !== 0) {
                session()->flash('error', 'Nincs lemondott előfizetés, amit újraaktiválhatnál.');
                return;
            }

            if ($this->nextPackageId) {
                session()->flash('error', 'Kérlek, mond le az előfizetési csomagváltást.');
                return;
            }

            StripeService::reactivateSubscription($this->subscription->stripe_subscription_id);

            $this->subscription->update([
                'status' => 'active',
                'auto_renewal' => true,
            ]);

            DB::commit();
            session()->flash('message', 'Az előfizetés sikeresen újraaktiválva.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Hiba az előfizetés újraaktiválása közben: ' . $e->getMessage());
            session()->flash('error', 'Hiba történt az előfizetés újraaktiválása közben: ' . $e->getMessage());
        }
    }

    #[On('confirmCancellation')]
    public function confirmCancellation($data)
    {
        $this->cancelSubscription($data['type']);
    }


    public function render()
    {
        return view('livewire.select-package');
    }
}
