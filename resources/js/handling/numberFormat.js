import { TokenService } from "@/services";


export function formatAmount(number) {
    const currency = TokenService.getCurrency();
    const symbol = 'Rp ';
    const position = currency.position;

    if (parseFloat(number) >= 0) {
        return position === 'before' ? symbol + ' ' + number.toLocaleString() : number.toLocaleString() + ' ' + symbol;
    } else {
        const formattedNumber = (-number).toLocaleString(); // Ambil nilai absolut dan format sebagai string
        return position === 'before' ? '-' + symbol + ' ' + formattedNumber : formattedNumber + ' -' + symbol;
    }
}
