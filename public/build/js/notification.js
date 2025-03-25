document.addEventListener('DOMContentLoaded', function() {

    let notificationModalEl = document.getElementById('notificationModal');
    let notificationModal = bootstrap.Modal.getInstance(notificationModalEl);
    if (!notificationModal) {
        notificationModal = new bootstrap.Modal(notificationModalEl);
    }

    const notifyList = document.querySelector('.notify-list');

    notifyList.addEventListener('click', function(event) {
        // Ha az x gombra kattintanak
        if (event.target.closest('.x-button')) {
            event.preventDefault();
            event.stopPropagation(); // Megakadályozza, hogy a notification-item click esemény is lefusson

            // Megkeressük a legközelebbi notification-item elemet
            const notificationItem = event.target.closest('.notification-item');
            const notificationId = notificationItem.dataset.id;

            // AJAX kérés a deleted mező módosításához
            fetch('/notifications/mark-as-deleted', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ id: notificationId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Eltávolítjuk a notification elemet a DOM-ból
                        notificationItem.remove();

                        if (document.querySelectorAll('.notification-item').length === 0) {
                            document.querySelector('.notify-list').innerHTML = '<div class="p-3 d-flex justify-content-center"><p>'+ translations.subscription_reminder +'</p></div>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Hiba történt:', error);
                });
            return;
        }

        // Ha nem az x gombra kattintanak, hanem az értesítésre
        if (event.target.closest('.notification-item')) {
            const notificationItem = event.target.closest('.notification-item');
            const notificationId = notificationItem.dataset.id;

            const isUnread = notificationItem.classList.contains('unread-notification');

            // AJAX kérés a read érték módosításához
            fetch('/notifications/get-modal-content', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ id: notificationId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {

                        document.querySelector('.notificationEvent').textContent = data.title;
                        document.getElementById('notificationStore').innerHTML = data.description;

                        notificationModal.show();

                        if(isUnread){
                            // Frissítjük az értesítés stílusát
                            notificationItem.classList.remove('unread-notification');
                            notificationItem.classList.add('read-notification');

                            // Ha van notify-dot, átalakítjuk x gombra (notify-close)
                            const notifyIndicator = notificationItem.querySelector('.notify-dot');
                            if (notifyIndicator) {
                                notifyIndicator.innerHTML = '<i class="material-icons-outlined fs-6 x-button">close</i>';
                                notifyIndicator.classList.remove('notify-dot');
                                notifyIndicator.classList.add('notify-close');
                            }

                            // Csökkentjük a badge számlálót
                            let badge = document.querySelector('.badge-notify');
                            if (badge) {
                                let count = parseInt(badge.textContent);
                                count = count > 0 ? count - 1 : 0;
                                if (count > 0) {
                                    badge.textContent = count;
                                } else {
                                    badge.remove();
                                }
                            }
                        }

                    }
                })
                .catch(error => {
                    console.error('Hiba történt:', error);
                });
        }
    });

    //Dropdown eltűnésének meggátolása ha modal vagy backdrop részre történt a kattintás
    const notificationsDropdown = document.querySelector('li.nav-item.dropdown');
    notificationsDropdown.addEventListener('hide.bs.dropdown', function(e) {
        if (e.clickEvent) {
            const target = e.clickEvent.target;
            if(target.closest('#notificationModal') || target.classList.contains('modal-backdrop')) {
                e.preventDefault();
            }
        }
    });


    // Összes értesítés olvasottként megjelölése
    const markAllReadBtn = document.getElementById('markAllRead');

    markAllReadBtn.addEventListener('click', function(event) {
        event.preventDefault();

        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            // Ha nincs szükség extra adatra, üres objektumot is küldhetünk
            body: JSON.stringify({})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Frissítjük a DOM-on belül az összes olvasatlan értesítést
                    document.querySelectorAll('.notification-item.unread-notification').forEach(item => {
                        item.classList.remove('unread-notification');
                        item.classList.add('read-notification');

                        // Ha van notify-dot elem, azt átalakítjuk x ikonra (olvasott)
                        const notifyIndicator = item.querySelector('.notify-dot');
                        if (notifyIndicator) {
                            notifyIndicator.innerHTML = '<i class="material-icons-outlined fs-6 x-button">close</i>';
                            notifyIndicator.classList.remove('notify-dot');
                            notifyIndicator.classList.add('notify-close');
                        }
                    });

                    const badge = document.querySelector('.badge-notify');
                    if (badge) {
                        badge.remove();
                    }

                } else {
                    toastr.error('Hiba történt a frissítés során.');
                }
            })
            .catch(error => {
                toastr.error('Hiba történt a frissítés során.');
            });
    });

    //Összes értesítés törlése
    const deleteAllBtn = document.getElementById('deleteAllNotifications');

    deleteAllBtn.addEventListener('click', function(event) {
        event.preventDefault();

        fetch('/notifications/delete-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const readNotifications = document.querySelectorAll('.notification-item.read-notification');
                    readNotifications.forEach(notification => notification.remove());

                    // Ellenőrizzük, hogy maradt-e még olvasatlan értesítés
                    const unreadCount = document.querySelectorAll('.notification-item.unread-notification').length;
                    const badge = document.querySelector('.badge-notify');

                    if (unreadCount > 0) {
                        // Ha vannak még olvasatlan értesítések, frissítjük a badge értékét
                        if (badge) {
                            badge.textContent = unreadCount;
                        }
                    } else {
                        // Ha nem maradt olvasatlan értesítés, eltávolítjuk a badge-et és megjelenítjük az üres állapotot
                        if (badge) {
                            badge.remove();
                        }
                        const notifyList = document.querySelector('.notify-list');
                        notifyList.innerHTML = '<div class="p-3 d-flex justify-content-center"><p>'+ translations.subscription_reminder +'</p></div>';
                    }
                } else {
                    toastr.error('Hiba történt a törlés során.');
                }
            })
            .catch(error => {
                toastr.error('Hiba történt a törlés során.');
            });
    });
});
