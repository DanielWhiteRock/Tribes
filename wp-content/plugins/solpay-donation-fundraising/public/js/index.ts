import SolpayDonationForm from "./src/solpay-donation-form";

export interface SolpayDonationWindow extends Window {
    SOLPAY_DONATION_FORM_SETTINGS: {
        verificationServiceUrl: string,
        verificationInterval: number,
        verificationTimeout: number,
        messages: {
            thankYouMessage: string,
            noAmountChosenMessage: string,
            chosenAmountMessage: string,
        }
    };
}

window.addEventListener('load', () => {
    SolpayDonationForm.init();
});
