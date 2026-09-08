const KEY = 'flash';

export function flash(message: string): void {
    sessionStorage.setItem(KEY, message);
}

export function takeFlash(): string | null {
    const message = sessionStorage.getItem(KEY);
    if (message !== null) {
        sessionStorage.removeItem(KEY);
    }
    return message;
}
