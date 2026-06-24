/** @type {import('expo/config').ExpoConfig} */
const projectId =
  process.env.EAS_PROJECT_ID ?? '15935d5e-21ff-47e9-9669-5860d96e792e';
const updatesUrl = `https://u.expo.dev/${projectId}`;

export default {
  expo: {
    name: 'Signature In House',
    slug: 'signature-in-house',
    version: '1.0.0',
    orientation: 'portrait',
    icon: './assets/icon.png',
    userInterfaceStyle: 'light',
    scheme: 'signatureinhouse',
    owner: process.env.EXPO_OWNER ?? 'sorath8461',
    runtimeVersion: {
      policy: 'appVersion',
    },
    ...(updatesUrl
      ? {
          updates: {
            url: updatesUrl,
            checkAutomatically: 'ON_LOAD',
            requestHeaders: {
              'expo-channel-name': 'preview',
            },
          },
        }
      : {}),
    extra: {
      laravelUrl: process.env.EXPO_PUBLIC_LARAVEL_URL ?? 'https://signature-in-house.com',
      eas: {
        projectId,
      },
    },
    ios: {
      supportsTablet: true,
      bundleIdentifier: 'com.signatureinhouse.app',
      buildNumber: '1',
      infoPlist: {
        ITSAppUsesNonExemptEncryption: false,
        NSCameraUsageDescription:
          'Allow camera access to upload photos in Signature In House.',
        NSPhotoLibraryUsageDescription:
          'Allow photo library access to upload images in Signature In House.',
      },
    },
    android: {
      package: 'com.signatureinhouse.app',
      versionCode: 1,
      permissions: ['INTERNET', 'ACCESS_NETWORK_STATE'],
      adaptiveIcon: {
        backgroundColor: '#E6F4FE',
        foregroundImage: './assets/android-icon-foreground.png',
        backgroundImage: './assets/android-icon-background.png',
        monochromeImage: './assets/android-icon-monochrome.png',
      },
      predictiveBackGestureEnabled: false,
    },
    web: {
      favicon: './assets/favicon.png',
    },
    plugins: ['expo-updates'],
  },
};
