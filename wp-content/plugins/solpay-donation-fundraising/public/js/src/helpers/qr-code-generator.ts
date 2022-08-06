import {createQR, encodeURL} from '@solana/pay';
import {TransactionType} from "./transaction-generator";

export default async function generateQrCode(transaction: TransactionType, qrCodeElementSelector: string): Promise<void> {
    const url = encodeURL(transaction);
    const qrCode = createQR(url);

    let qrCodeElement = document.querySelector(qrCodeElementSelector);

    if (!qrCodeElement) {
        qrCodeElement = document.createElement('img');
        document.body.prepend(qrCodeElement);
    }

    (qrCodeElement as HTMLImageElement).src =  URL.createObjectURL(await qrCode.getRawData());
}

export function updateQrCodeLink(transaction: TransactionType, linkElementSelector: string): void {
    (document.querySelector(linkElementSelector) as HTMLAnchorElement).href = encodeURL(transaction);
}

