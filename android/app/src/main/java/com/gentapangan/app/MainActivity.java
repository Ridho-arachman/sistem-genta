package com.gentapangan.app;

import android.os.Bundle;
import android.webkit.WebSettings;
import com.getcapacitor.BridgeActivity;

public class MainActivity extends BridgeActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        WebSettings webSettings = getBridge().getWebView().getSettings();
        webSettings.setGeolocationEnabled(true);
        webSettings.setUserAgentString(webSettings.getUserAgentString() + " GentaCapacitor/1.0");
    }
}