/**
 * Internal dependencies
 */
import { PAYMENT_METHOD_NAME } from './constants';
import { PaymentRequestExpress } from './payment-request-express';
import { applePayImage } from './apple-pay-preview';
import { loadStripe } from '../stripe-utils';

const ApplePayPreview = () => <img src={ applePayImage } alt="" />;

const canPayStripePromise = loadStripe();
const componentStripePromise = loadStripe();

const PaymentRequestPaymentMethod = {
	id: PAYMENT_METHOD_NAME,
	content: <PaymentRequestExpress stripe={ componentStripePromise } />,
	edit: <ApplePayPreview />,
	canMakePayment: ( cartData ) =>
		canPayStripePromise.then( ( stripe ) => {
			if ( stripe === null ) {
				return false;
			}
			// do a test payment request to check if payment request payment can be
			// done.
			// @todo, initial country and currency needs to server.
			const paymentRequest = stripe.paymentRequest( {
				total: {
					label: 'Test total',
					amount: 1000,
				},
				country: cartData?.shippingAddress?.country,
				// eslint-disable-next-line camelcase
				currency: cartData?.cartTotals?.currency_code?.toLowerCase(),
			} );
			return paymentRequest
				.canMakePayment()
				.then( ( result ) => !! result );
		} ),
};

export default PaymentRequestPaymentMethod;