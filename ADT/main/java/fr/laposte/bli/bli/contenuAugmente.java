package fr.laposte.bli.bli;

import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.Snackbar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;

/**
 * Created by XKRC310 on 22/06/2016.
 */
public class contenuAugmente extends MainActivity {


        ////////////////////////////////////////////////////////////////////////
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.contenuaugmente);
        String URL = getIntent().getExtras().getString("url");
        WebView mWebView = (WebView) findViewById ( R.id.webView );

        WebSettings webSettings = mWebView.getSettings();
        webSettings.setJavaScriptEnabled(true);

        mWebView.setWebViewClient(new WebViewClient());
        mWebView.loadUrl(URL);



    }

}
