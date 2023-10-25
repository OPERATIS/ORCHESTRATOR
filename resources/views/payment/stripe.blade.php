<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <button style="display:none;"
                data-publishable-key="{{$publishableKey}}"
                data-session-id="{{$sessionId}}" id="checkout-button">
        </button>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function (event) {
                const checkoutButton = document.getElementById('checkout-button');
                const stripe = Stripe(checkoutButton.getAttribute('data-publishable-key'));
                checkoutButton.addEventListener('click', function () {
                    stripe.redirectToCheckout({
                        sessionId: checkoutButton.getAttribute('data-session-id')
                    }).then(function (result) {
                        console.log(result.error.message);
                    });
                });
                checkoutButton.click();
            });
        </script>
    </body>
</html>
