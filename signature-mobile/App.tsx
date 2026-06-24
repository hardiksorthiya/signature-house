import { StatusBar } from 'expo-status-bar';
import { useCallback, useEffect, useRef, useState } from 'react';
import {
  ActivityIndicator,
  BackHandler,
  Platform,
  Pressable,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';
import WebView, { type WebViewNavigation } from 'react-native-webview';
import type { WebViewErrorEvent } from 'react-native-webview/lib/WebViewTypes';
import { isAllowedHost, LARAVEL_URL } from './src/config';

export default function App() {
  const webViewRef = useRef<WebView>(null);
  const [canGoBack, setCanGoBack] = useState(false);
  const [loading, setLoading] = useState(true);
  const [loadError, setLoadError] = useState<string | null>(null);

  const handleAndroidBack = useCallback(() => {
    if (canGoBack && webViewRef.current) {
      webViewRef.current.goBack();
      return true;
    }
    return false;
  }, [canGoBack]);

  useEffect(() => {
    if (Platform.OS !== 'android') {
      return;
    }
    const sub = BackHandler.addEventListener('hardwareBackPress', handleAndroidBack);
    return () => sub.remove();
  }, [handleAndroidBack]);

  const onNavigationStateChange = (nav: WebViewNavigation) => {
    setCanGoBack(nav.canGoBack);
  };

  const onError = (event: WebViewErrorEvent) => {
    setLoading(false);
    setLoadError(event.nativeEvent.description || 'Could not load the app.');
  };

  const reload = () => {
    setLoadError(null);
    setLoading(true);
    webViewRef.current?.reload();
  };

  return (
    <SafeAreaProvider>
      <SafeAreaView style={styles.container} edges={['top', 'left', 'right']}>
        <StatusBar style="dark" />

        {loadError ? (
          <View style={styles.errorBox}>
            <Text style={styles.errorTitle}>Connection problem</Text>
            <Text style={styles.errorText}>{loadError}</Text>
            <Pressable style={styles.retryButton} onPress={reload}>
              <Text style={styles.retryButtonText}>Try again</Text>
            </Pressable>
          </View>
        ) : (
          <>
            <WebView
              ref={webViewRef}
              source={{ uri: LARAVEL_URL }}
              style={styles.webview}
              startInLoadingState
              sharedCookiesEnabled
              thirdPartyCookiesEnabled
              domStorageEnabled
              javaScriptEnabled
              allowsBackForwardNavigationGestures
              setSupportMultipleWindows={false}
              originWhitelist={['https://*', 'http://*']}
              onNavigationStateChange={onNavigationStateChange}
              onLoadStart={() => {
                setLoading(true);
                setLoadError(null);
              }}
              onLoadEnd={() => setLoading(false)}
              onError={onError}
              onHttpError={(syntheticEvent) => {
                const status = syntheticEvent.nativeEvent.statusCode;
                if (status >= 400) {
                  setLoadError(`Server returned error ${status}.`);
                  setLoading(false);
                }
              }}
              onShouldStartLoadWithRequest={(request) => {
                if (request.url.startsWith('about:blank')) {
                  return true;
                }
                return isAllowedHost(request.url);
              }}
              userAgent={
                Platform.OS === 'android'
                  ? undefined
                  : 'SignatureInHouseApp/1.0 (Mobile; Expo)'
              }
            />

            {loading && (
              <View style={styles.loadingOverlay}>
                <ActivityIndicator size="large" color="#e74743" />
                <Text style={styles.loadingText}>Loading Signature In House…</Text>
              </View>
            )}
          </>
        )}
      </SafeAreaView>
    </SafeAreaProvider>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#ffffff',
  },
  webview: {
    flex: 1,
    backgroundColor: '#ffffff',
  },
  loadingOverlay: {
    ...StyleSheet.absoluteFill,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: 'rgba(255,255,255,0.92)',
    gap: 12,
  },
  loadingText: {
    color: '#374151',
    fontSize: 14,
  },
  errorBox: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: 24,
    gap: 12,
  },
  errorTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#1f2937',
  },
  errorText: {
    fontSize: 14,
    color: '#6b7280',
    textAlign: 'center',
  },
  retryButton: {
    marginTop: 8,
    backgroundColor: '#e74743',
    paddingHorizontal: 20,
    paddingVertical: 12,
    borderRadius: 8,
  },
  retryButtonText: {
    color: '#ffffff',
    fontWeight: '600',
  },
});
