import {generateTransaction} from "./helpers/transaction-generator";
import generateQrCode, {updateQrCodeLink} from "./helpers/qr-code-generator";
import SolpayDonationVerifier from "./solpay-donation-verifier";
import {SolpayDonationWindow} from "../index";

declare const window: SolpayDonationWindow;

export default class SolpayDonationForm {
    private donateButton: HTMLButtonElement;
    private amountInfos: Array<HTMLElement>;
    private amountTypeInputs: Array<HTMLInputElement>;
    private predefinedAmountInputs: HTMLInputElement[];
    private customAmountInput: HTMLInputElement | null;

    constructor() {
        this.donateButton = document.querySelector('.js-solpay-donate-button') as HTMLButtonElement;
        this.amountInfos = Array.from(document.querySelectorAll('.js-amount-info')) as Array<HTMLElement>;
        this.amountTypeInputs = Array.from(document.querySelectorAll('.js-change-amount-type')) as Array<HTMLInputElement>;
        this.predefinedAmountInputs = Array.from(document.querySelectorAll('[name="solpay_donation_predefined_amount"]')) as Array<HTMLInputElement>;
        this.customAmountInput = document.querySelector('[name="solpay_donation_custom_amount"]') as HTMLInputElement | null;

        this.initEvents();
    }

    private initEvents() {
        this.donateButton && this.donateButton.addEventListener('click', () => this.updateAndShowQr());

        this.predefinedAmountInputs.forEach(
            predefinedAmountInput => predefinedAmountInput.addEventListener('click', () => {
                this.updateState('predefined', parseFloat(predefinedAmountInput.value) || 0);
            })
        );

        this.customAmountInput && this.customAmountInput.addEventListener('focus', () => {
            this.updateState('custom', parseFloat(this.customAmountInput.value) || 0);
        });

        this.customAmountInput && this.customAmountInput.addEventListener('input', () => {
            this.updateState('custom', parseFloat(this.customAmountInput.value) || 0);
        });

        this.amountTypeInputs.forEach(
            amountTypeInput => amountTypeInput.addEventListener('click', () => {
                this.updateState(amountTypeInput.value as 'custom' | 'predefined', 0);
            })
        );
    }

    private async updateAndShowQr(): Promise<void> {
        const amount = this.getAmount();
        const recipient = (document.querySelector('[name="solpay_donation_wallet"]') as HTMLInputElement).value;
        const splToken = (document.querySelector('[name="solpay_donation_spl_token"]') as HTMLInputElement).value;
        const label = (document.querySelector('[name="solpay_donation_label"]') as HTMLInputElement).value;
        const message = '';
        const memo = '';

        if (!amount) {
            return;
        }

        try {
            const transaction = await generateTransaction({
                recipient,
                splToken,
                amount,
                label,
                message,
                memo,
            });

            await generateQrCode(transaction, '.js-solpay-donation-qr');
            updateQrCodeLink(transaction, '.js-solpay-donation-deeplink');

            SolpayDonationForm.hideAmountStep();
            SolpayDonationForm.showQrStep();

            SolpayDonationVerifier.init(
                Object.assign({transaction: transaction}, window.SOLPAY_DONATION_FORM_SETTINGS)
            );
        } catch (e) {
            console.log(e);
        }
    }

    private updateState(amountType: 'custom' | 'predefined', amount: number): void {
        this.updateAmountType(amountType);
        this.updateAmountInfo(amount);
        this.updateDonationButton(amount);
    }

    private updateAmountType(type: 'custom' | 'predefined'): void {
        this.amountTypeInputs.forEach(amountTypeInput => amountTypeInput.checked = type === amountTypeInput.value);

        if (type === 'custom') {
            this.predefinedAmountInputs.forEach(
                predefinedAmountInput => predefinedAmountInput.checked = false
            );
        } else {
            this.customAmountInput && (this.customAmountInput.value = '');
        }
    }

    private updateAmountInfo(amount: number): void {
        if (amount === 0 || isNaN(amount)) {
            this.amountInfos.forEach(
                amountInfo => amountInfo.innerText = window.SOLPAY_DONATION_FORM_SETTINGS.messages.noAmountChosenMessage
            );

            return;
        }

        const updateAmountInfoElement = (amountInfo) => {
            amountInfo.innerHTML = (new DOMParser()).parseFromString(
                window.SOLPAY_DONATION_FORM_SETTINGS.messages.chosenAmountMessage, 'text/html'
            ).documentElement.textContent;

            (amountInfo.querySelector('.js-amount-value') as HTMLElement).innerText = amount.toString();
        };

        this.amountInfos.forEach(updateAmountInfoElement);
    }

    private updateDonationButton(amount: number): void {
        this.donateButton.disabled = amount <= 0;
    }

    private getAmount(): number {
        let amount = 0;

        let selectedPredefinedAmount = document.querySelector('[name="solpay_donation_predefined_amount"]:checked') as HTMLInputElement | undefined;

        if (selectedPredefinedAmount) {
            amount = parseFloat(selectedPredefinedAmount.value) || 0;
        }

        if (!amount) {
            amount = parseFloat(this.customAmountInput.value) || 0;
        }

        return amount;
    }

    public static showQrStep(): void {
        document.querySelector('.solpay-donation-form__qr-container')
            .classList.remove('solpay-donation-form__qr-container--hidden');
    }

    public static hideQrStep(): void {
        document.querySelector('.solpay-donation-form__qr-container')
            .classList.add('solpay-donation-form__qr-container--hidden');
    }

    public static showAmountStep(): void {
        document.querySelector('.solpay-donation-form__amount-container')
            .classList.remove('solpay-donation-form__amount-container--hidden');
    }

    public static hideAmountStep(): void {
        document.querySelector('.solpay-donation-form__amount-container')
            .classList.add('solpay-donation-form__amount-container--hidden');
    }

    public static showFeedbackMessage() {
        const feedbackMessageElement = document.querySelector('.solpay-donation-form__feedback-message');

        feedbackMessageElement.innerHTML = (new DOMParser()).parseFromString(
            window.SOLPAY_DONATION_FORM_SETTINGS.messages.thankYouMessage, 'text/html'
        ).documentElement.textContent;

        feedbackMessageElement.classList.remove('solpay-donation-form__feedback-message--hidden');
    }

    public static hideFeedbackMessage() {
        const feedbackMessageElement = document.querySelector('.solpay-donation-form__feedback-message');

        feedbackMessageElement.classList.add('solpay-donation-form__feedback-message--hidden');
    }

    public static init() {
        new SolpayDonationForm();
    }
}
