document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll('.paypal-button');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const amount = button.getAttribute('data-amount');
            const description = button.getAttribute('data-description');
            const duration = button.getAttribute('data-duration');

            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: amount
                            },
                            description: description
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        alert('Transaction completed by ' + details.payer.name.given_name);
                        // Envoyer les détails de la transaction au serveur pour mettre à jour la base de données
                        fetch('update_vip.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                payerID: details.payer.payer_id,
                                duration: duration,
                                transactionID: details.id
                            })
                        }).then(response => response.json()).then(data => {
                            if (data.success) {
                                alert('VIP status updated successfully.');
                            } else {
                                alert('Failed to update VIP status.');
                            }
                        });
                    });
                }
            }).render('body');
        });
    });
});
