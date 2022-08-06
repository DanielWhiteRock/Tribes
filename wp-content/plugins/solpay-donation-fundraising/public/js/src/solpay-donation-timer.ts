type SolpayDonationTimerConfigType = {
    timeout: number;
    timeoutCallback: any
};

export default class SolpayDonationTimer {
    private config: SolpayDonationTimerConfigType;
    private intervalTimer: any;
    private timeoutTimer: any;
    private timerElement: Element|null;
    private remainingTime: number;

    constructor(config: SolpayDonationTimerConfigType) {
        this.config = config;
        this.timerElement = document.querySelector('.js-solpay-donation-timer');
        this.remainingTime = this.config.timeout;

        this.start();
    }

    public start(): void {
        this.intervalTimer = setInterval(() => this.tick(), 1000);
        this.timeoutTimer = setTimeout(() => {
            clearInterval(this.intervalTimer);
            this.config.timeoutCallback();
        }, this.config.timeout);
    }

    public stop(): void {
        clearInterval(this.intervalTimer);
        clearTimeout(this.timeoutTimer);
    }

    private tick() {
        this.updateRemainingTime();
        this.updateTimerElement();
    }

    private updateRemainingTime() {
        this.remainingTime -= 1000;
    }

    private updateTimerElement() {
        if (this.timerElement === null) {
            return;
        }

        this.timerElement.innerHTML = new Date(this.remainingTime).toTimeString().substring(3, 8);
    }
}
