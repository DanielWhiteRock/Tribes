import SolpayDonationTimer from "./solpay-donation-timer";
import {TransactionType} from "./helpers/transaction-generator";
import SolpayDonationForm from "./solpay-donation-form";

type SolpayDonationVerifierConfigType = {
    verificationServiceUrl: string,
    verificationInterval: number,
    verificationTimeout: number,
    transaction: TransactionType,
    messages: {
        thankYouMessage: string
    },
};

export default class SolpayDonationVerifier {
    private config: SolpayDonationVerifierConfigType;
    private verificationTimer: any;
    private timeoutTimer: SolpayDonationTimer;

    constructor(config: SolpayDonationVerifierConfigType) {
        this.config = config;

        SolpayDonationForm.hideFeedbackMessage();

        this.startVerification().catch(console.log);

        this.timeoutTimer = new SolpayDonationTimer({
            timeout: this.config.verificationTimeout,
            timeoutCallback: this.clearVerificationTimer.bind(this)
        });
    }

    private async startVerification(): Promise<any> {
        return new Promise((resolve, reject) => {
            this.verificationTimer = setInterval(async () => {
                try {
                    let data = await this.verifyTransaction();

                    if (data.success) {
                        this.clearVerificationTimer();
                        resolve(data);
                    }
                } catch (e: any) {
                    this.clearVerificationTimer();
                    reject(e);
                }
            }, this.config.verificationInterval);
        });
    }

    public async verifyTransaction(): Promise<{ success?: boolean }> {
        let queryParams = new URLSearchParams(this.getTransactionVerificationParameters());
        return await (await fetch(`${this.config.verificationServiceUrl}?${queryParams.toString()}`)).json();
    }

    public clearVerificationTimer(): void {
        this.timeoutTimer.stop();
        clearInterval(this.verificationTimer);
        this.showFeedbackMessage();
    }

    private async showFeedbackMessage(): Promise<void> {
        let data = await this.verifyTransaction();

        SolpayDonationForm.hideQrStep();

        if (data.success) {
            SolpayDonationForm.showFeedbackMessage();
        } else {
            console.log('Donation not confirmed.');

            SolpayDonationForm.showAmountStep();
        }
    }

    private getTransactionVerificationParameters(): any {
        const transaction = this.config.transaction;

        let params: any = {
            reference: transaction.reference,
            recipient: transaction.recipient,
            amount: transaction.amount,
            label: transaction.label,
            message: transaction.message,
            memo: transaction.memo,
        }

        if (transaction.splToken) {
            params.splToken = transaction.splToken.toString();
        }

        return params;
    }

    public static init(config: SolpayDonationVerifierConfigType): SolpayDonationVerifier {
        return new SolpayDonationVerifier(config);
    }
}
