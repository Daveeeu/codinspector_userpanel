<?php

namespace App\Livewire;

use App\Models\BillingInfo;
use App\Models\Package;
use App\Models\Store;
use App\Stripe\StripeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\Platform;
use App\Models\Subscription;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class StoreForm extends Component
{
    public $step = 1;
    public $platform_id, $domain, $lost_package_cost = 2500, $company_name, $tax_id, $address, $city, $postal_code, $country, $selectedPackageId;
    public $platforms;
    public $packages;
    public $payment_method;
    public $coupon_code;
    public $paymentFrequency = 'monthly';

    public function mount()
    {
        $this->platforms = Platform::all();
        $this->packages = Package::all();
    }

    public function nextStep()
    {
        $this->dispatch('stepUpdated');
        $this->validateStep();
        $this->step++;

        Activity::create([
            'causer_id' => auth()->id(),
            'properties' => ['step' => $this->step],
            'description' => 'User moved to the next step',
        ]);
    }

    public function prevStep()
    {
        $this->dispatch('stepUpdated');
        $this->step--;

        Activity::create([
            'causer_id' => auth()->id(),
            'properties' => ['step' => $this->step],
            'description' => 'User moved to the previous step',
        ]);
    }

    private function validateStep()
    {
        $validationRules = [];
        if ($this->step == 1) {
            $validationRules = [
                'platform_id' => 'required|exists:platforms,platform_id',
            ];
            $this->validate($validationRules);
        } elseif ($this->step == 2) {
            $validationRules = [
                'domain' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:stores,domain',
                    function ($attribute, $value, $fail) {
                        if (!filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                            $fail("The {$attribute} must be a valid domain name.");
                            return;
                        }
                    },
                ],
                'lost_package_cost' => 'nullable|numeric|min:0',
            ];

            $this->validate($validationRules);
        } elseif ($this->step == 4) {
            $validationRules = [
                'company_name' => 'required|string|max:50',
                'tax_id' => 'required|string|max:20',
                'address' => 'required|string|max:40',
                'city' => 'required|string|max:40',
                'postal_code' => 'required|string|max:10',
                'country' => 'required|string|max:50',
            ];
            $this->validate($validationRules);
        } elseif ($this->step == 4) {
            $validationRules = [];
        }
    }

    public function createStore()
    {
        DB::beginTransaction();

        try {
            $billingInfo = BillingInfo::create([
                'user_id' => auth()->id(),
                'company_name' => $this->company_name,
                'tax_id' => $this->tax_id,
                'address' => $this->address,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
            ]);

            Activity::create([
                'causer_id' => auth()->id(),
                'properties' => ['billing_info' => $billingInfo],
                'description' => 'Billing info created',
            ]);

            StripeService::setApiKey(config('services.stripe.secret'));
            $user = auth()->user();
            $customer = StripeService::createCustomerIfNotExists($user);
            $package = Package::find($this->selectedPackageId);

            if (!$package) {
                throw new \Exception('Invalid package selected');
            }

            $trialDays = Subscription::where('user_id', auth()->id())->exists() ? 0 : (int)env('TRIAL_PERIOD_DAYS', 14);

            if ($package->cost_per_query) {
                $stripeSubscription = StripeService::createUsageBasedSubscription($customer, $package, $trialDays, $this->applied_coupon ?? null);
                $subscriptionItem = $stripeSubscription->items->data[0]->id;

                $subscription = Subscription::create([
                    'user_id' => auth()->id(),
                    'package_id' => $this->selectedPackageId,
                    'price' => $package->cost_per_query,
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'auto_renewal' => true,
                    'status' => 'active',
                    'stripe_subscription_id' => $stripeSubscription->id,
                    'stripe_subscription_item_id' => $subscriptionItem,
                    'usage_based_billing' => true,
                ]);
            } else {
                $stripeSubscription = StripeService::createSubscription($customer, $package, $this->paymentFrequency, $trialDays, $this->applied_coupon ?? null);


                $subscription = Subscription::create([
                    'user_id' => auth()->id(),
                    'package_id' => $this->selectedPackageId,
                    'price' => $package->cost,
                    'start_date' => now(),
                    'end_date' => $this->paymentFrequency === "monthly" ? now()->addMonth() : now()->addYear(),
                    'auto_renewal' => true,
                    'status' => 'active',
                    'payment_frequency' => $this->paymentFrequency,
                    'stripe_subscription_id' => $stripeSubscription->id,
                ]);
            }

            $apiKey = Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4));
            $apiSecret = Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4));
            $store = Store::create([
                'user_id' => auth()->id(),
                'platform_id' => $this->platform_id,
                'domain' => $this->domain,
                'lost_package_cost' => $this->lost_package_cost,
                'subscription_id' => $subscription->subscription_id,
                'billing_id' => $billingInfo->billing_id,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ]);

            Activity::create([
                'causer_id' => auth()->id(),
                'properties' => ['store' => $store],
                'description' => 'Store created',
            ]);

            DB::commit();

            $this->dispatch('store-created', [
                'message' => 'Az üzlet sikeresen létrejött!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Activity::create([
                'causer_id' => auth()->id(),
                'properties' => ['error_message' => $e->getMessage()],
                'description' => 'Error occurred during store creation',
            ]);

            Log::info($e->getMessage());
            throw ValidationException::withMessages([
                'error' => 'An error occurred while saving the store and subscription: ' . $e->getMessage(),
            ]);
        }
    }

    public function applyCoupon()
    {

        if (empty($this->coupon_code)) {
            session()->flash('coupon_error', 'Kérjük, adjon meg egy kuponkódot.');
            $this->dispatch('init-stripe-payment');
            return;
        }

        try {
            StripeService::setApiKey(config('services.stripe.secret'));
            $coupon = StripeService::retrieveCoupon($this->coupon_code);

            if (!$coupon->valid) {
                session()->flash('coupon_error', 'A kupon lejárt vagy érvénytelen.');
                $this->dispatch('init-stripe-payment');
                return;
            }

            session()->flash('coupon_success', 'A kupon sikeresen alkalmazva!');
            $this->applied_coupon = $this->coupon_code;
            $this->dispatch('init-stripe-payment');

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Nem létező kuponkód vagy hibás kérés
            session()->flash('coupon_error', 'A megadott kuponkód nem található.');
            $this->dispatch('init-stripe-payment');
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // API kulcs hiba
            session()->flash('coupon_error', 'Hitelesítési hiba történt. Kérjük, próbálja újra később.');
            $this->dispatch('init-stripe-payment');
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Hálózati hiba
            session()->flash('coupon_error', 'Kapcsolódási hiba történt. Kérjük, ellenőrizze internetkapcsolatát.');
            $this->dispatch('init-stripe-payment');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Egyéb Stripe API hiba
            session()->flash('coupon_error', 'Hiba történt a kupon ellenőrzése során. Kérjük, próbálja újra később.');
            $this->dispatch('init-stripe-payment');
        } catch (\Exception $e) {
            // Bármilyen más hiba
            session()->flash('coupon_error', 'Váratlan hiba történt a kupon ellenőrzése során.');
            $this->dispatch('init-stripe-payment');
        }
    }


    public function storePaymentMethod($paymentMethodId)
    {

            $validationRules = [
                'company_name' => 'required|string|max:50',
                'tax_id' => 'required|string|max:20',
                'address' => 'required|string|max:40',
                'city' => 'required|string|max:40',
                'postal_code' => 'required|string|max:10',
                'country' => 'required|string|max:50',
            ];


            //$this->dispatch('init-stripe-payment');
        $this->validate($validationRules);
        try {
            StripeService::setApiKey(config('services.stripe.secret'));

            $user = auth()->user();
            $customer = StripeService::createCustomerIfNotExists($user);
            $paymentMethod = StripeService::storePaymentMethod($paymentMethodId, $customer);

            Activity::create([
                'causer_id' => auth()->id(),
                'properties' => [
                    'payment_method_id' => $paymentMethodId,
                    'customer_id' => $customer->id,
                ],
                'description' => 'Payment method stored',
            ]);

            session()->flash('success', 'Payment method successfully added!');
            $this->createStore();
        }catch (\Stripe\Exception\ApiErrorException $e) {
            Activity::create([
                'causer_id' => auth()->id(),
                'properties' => ['error_message' => $e->getMessage()],
                'description' => 'Stripe API error during payment method storage',
            ]);
            session()->flash('error', 'Stripe error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Activity::create([
                'causer_id' => auth()->id(),
                'properties' => ['error_message' => $e->getMessage()],
                'description' => 'General error during payment method storage',
            ]);
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function togglePaymentFrequency()
    {
        $this->paymentFrequency = $this->paymentFrequency === 'monthly' ? 'annually' : 'monthly';
    }

    public function render()
    {
        return view('livewire.store-form');
    }

    public function selectSubscription($packageId)
    {
        $package = Package::query()->where('package_id', $packageId);
        if ($package->exists()) {
            $this->selectedPackageId = $packageId;
        }
    }
}
