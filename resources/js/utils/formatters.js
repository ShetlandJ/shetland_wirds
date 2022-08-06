import format from 'date-fns/format';
import DOMPurify from 'dompurify';

const DATE_FORMAT = 'd MMM yy';
const TIME_FORMAT = 'HH:mm';
const DATE_TIME_FORMAT = 'd MMM yy - HH:mm';

export const formatDate = date => {
    return format(new Date(date), DATE_FORMAT);
}

export const formatTime = date => format(date, TIME_FORMAT);

export const formatDateTime = date => {
    return format(new Date(date), DATE_TIME_FORMAT)
};

export function formatMinsAndSecs(secs) {
    function zeroPad(num, places) {
        const zero = places - num.toString().length + 1;
        return Array(+(zero > 0 && zero)).join('0') + num;
    }

    const m = Math.floor(secs / 60);
    const s = secs % 60;

    return `${zeroPad(m, 2)}:${zeroPad(s, 2)}`;
}

export function capitalize(value) {
    if (!value) {
        return '';
    }

    const valueStr = value.toString();

    return valueStr.charAt(0).toUpperCase() + valueStr.slice(1);
}

export function sanitiseString(string) {
    if (!string) return '';

    return DOMPurify.sanitize(string, { ADD_ATTR: ['target'] });
}
