import Constants from 'expo-constants';

const extra = Constants.expoConfig?.extra as { laravelUrl?: string } | undefined;

export const LARAVEL_URL =
  process.env.EXPO_PUBLIC_LARAVEL_URL ??
  extra?.laravelUrl ??
  'https://signature-in-house.com';

export function isAllowedHost(url: string): boolean {
  try {
    const host = new URL(url).hostname.replace(/^www\./, '');
    const allowed = new URL(LARAVEL_URL).hostname.replace(/^www\./, '');
    return host === allowed || host.endsWith(`.${allowed}`);
  } catch {
    return false;
  }
}
