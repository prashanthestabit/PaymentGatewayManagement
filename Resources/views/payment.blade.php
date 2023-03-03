<form method="POST" action="/checkout">
    {{ csrf_field() }}

    <div>
        <label for="amount">Amount:</label>
        <input type="text" id="amount" name="amount">
    </div>

    <div>
        <label for="nonce">PayPal Nonce:</label>
        <input type="text" id="nonce" name="nonce" value="fake-valid-paypal-nonce">
    </div>

    <button type="submit">Pay with PayPal</button>
</form>
