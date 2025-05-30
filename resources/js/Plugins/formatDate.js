import dayjs from 'dayjs';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';
dayjs.extend(utc);
dayjs.extend(timezone);

export function formatDate(date) {
    if(!date) return "-";
    return dayjs(date).tz('Asia/Jakarta').format("DD MMMM YYYY HH:mm:ss");
}