{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<h3>Please wait, redirecting to process payment..</h3>
{/block}

{block name=script}
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('{$stripe_api_key}');
        
        stripe.redirectToCheckout({
          sessionId: '{$checkout_session_id}'
        }).then(function (result) {
          console.log(result);
          // If `redirectToCheckout` fails due to a browser or network
          // error, display the localized error message to your customer
          // using `result.error.message`.
        });
        
    </script>
{/block}