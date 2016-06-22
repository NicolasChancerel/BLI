package fr.laposte.bli.bli;

import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.StrictMode;
import android.os.Vibrator;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
//////////////////////////////////////
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
//////////////////////////////////////
import android.app.NotificationManager;
import android.app.Notification;
////////////////////////////////////////
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
//////////////////////////////////////
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONArray;

public class MainActivity extends AppCompatActivity {

    //private static final String TAG_CLEF_PARAMS_ = "";
    private static final String TAG_RFID = "rfid";
    private static final String TAG_FIELD = "fields";
    private static final String TAG_CLEF_PARAMS = "cle_params";
    private static final String TAG_REF = "libelle_params";
    private static final String TAG_VALEURS = "valeur";
    private static final String TAG_DATE_CREATION = "date_creation";
    private static final String TIME = "timestamp";
    private static final String RFID_values = "values";

    private static final String attm2x_API_KEY = "0cf6abec0e9ea8f4bd8f2d267bb2eacf";
    private static final String attm2x_DEVICE_ID = "07966069e262d4bd198568352c3a4318";
    private static final String attm2x_DATE_FORMAT = "yyyy-MM-dd'T'HH:mm:ss'.000+00:00'";   //2016-06-14T09:50:11.000+00:00

    String NULL = " ";
    String TAGRFID = " ";
    String NOM_CLIENT = " ";
    String NOM_PRODUIT = " ";
    String REF_PRODUIT = " ";
    String URL_MANUEL = " ";
    String DATE_CREATION = " ";

    Long last_RFID_TimeStamp = new Long(0);

    ////////////////////////////////////////////////////////////////////////
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        ////////////////////////////////////////////////////////////////////////
        Button contenuAugmente = (Button) findViewById ( R.id.contenuAugmente );
        if (contenuAugmente != null) {
            contenuAugmente.setOnClickListener(new Button.OnClickListener() {
                @Override
                public void onClick(View v) {
                    // Changer de vue vers jeu

                    if (URL_MANUEL != NULL) {
                        Toast.makeText(getBaseContext(), "affichage du manuel", Toast.LENGTH_SHORT).show();
                        Intent intent = new Intent(MainActivity.this, contenuAugmente.class);
                        intent.putExtra("url" , URL_MANUEL);
                        startActivity(intent);
                    }

                    else {
                        Toast.makeText(getBaseContext(), "Aucun Lien internet", Toast.LENGTH_SHORT).show();
                    }
                }
            });
        }
        ////////////////////////////////////////////////////////////////////////

        new Thread(new Runnable(){
            public void run() {
                // TODO Auto-generated method stub
                boolean clearFirst = false;
                while(true)
                {

                    boolean messageStatut = LookNewRFID("http://testm2x.herokuapp.com/ws/getAllMessages.php", "RFID");

                    if (clearFirst == true) {
                        if (messageStatut) {

                            decompile(GetTagRFIDcontent("http://testm2x.herokuapp.com/rfid/ws/afficheRfidById.php", TAGRFID));
                            createNotification();
                            setData();


                            try {
                                Thread.sleep(10000);
                            } catch (InterruptedException e) {
                                e.printStackTrace();
                            }
                        } else {
                            try {
                                Thread.sleep(10000);
                            } catch (InterruptedException e) {
                                e.printStackTrace();
                            }
                        }
                        messageStatut = false;
                    }else {
                        clearFirst = true;
                    }

                }

            }
        }).start();
        ////////////////////////////////////////////////////////////////////////
    }

    public String getUrlManuel(){return URL_MANUEL;}

    private void decompile(String _response) {

        JSONObject json_response;

        try {

            json_response = new JSONObject(_response);

            Log.d("My App", json_response.toString());

            // Getting Json ARRAY node
            JSONObject RFID = json_response.getJSONObject(TAG_RFID);

            DATE_CREATION = RFID.getString(TAG_DATE_CREATION);

            JSONArray fields = RFID.getJSONArray(TAG_FIELD);
            for (int i = 0; i < fields.length(); i++) {
                JSONObject c = fields.getJSONObject(i);


                // ZONE DE TEST ///


                ///////////////////

                if (i == 0) { NOM_CLIENT = c.getString(TAG_VALEURS);}

                if (i == 1) { NOM_PRODUIT = c.getString(TAG_VALEURS);}

                if (i == 2) { REF_PRODUIT = c.getString(TAG_VALEURS);}

                if (i == 3) { URL_MANUEL = c.getString(TAG_VALEURS);}

            }

        } catch (JSONException e) {
            e.printStackTrace();
        }

    }
    ////////////////////////////////////////////////////////////////////////


    ////////////////////////////////////////////////////////////////////////
    private void createNotification(){
        Intent resultIntent = new Intent(this, MainActivity.class);

        Vibrator v = (Vibrator)getSystemService(Context.VIBRATOR_SERVICE);


        PendingIntent resultPendingIntent =
                PendingIntent.getActivity(
                        this,
                        0,
                        resultIntent,
                        PendingIntent.FLAG_UPDATE_CURRENT
                );

        String ContentTitle = "Hey ! Votre ";
        ContentTitle += NOM_PRODUIT;

        String ContentText = NOM_CLIENT;
        ContentText += " ";
        ContentText += REF_PRODUIT;



        Notification.Builder mBuilder =
                new Notification.Builder(this)
                        .setSmallIcon(R.drawable.logo)
                        .setContentTitle(ContentTitle)
                        .setContentText(ContentText);
        mBuilder.setContentIntent(resultPendingIntent);
        Notification notif = mBuilder.build();
        notif.flags = Notification.DEFAULT_LIGHTS | Notification.FLAG_AUTO_CANCEL;
        mBuilder.build().flags = Notification.DEFAULT_LIGHTS | Notification.FLAG_AUTO_CANCEL;
        NotificationManager mNotifyMgr =
                (NotificationManager) getSystemService(NOTIFICATION_SERVICE);


        v.vibrate(500);
        mNotifyMgr.notify(1, notif);

    }
    ////////////////////////////////////////////////////////////////////////


    ////////////////////////////////////////////////////////////////////////
    private void setData(){


        runOnUiThread(new Runnable() {
            @Override
            public void run() {
                // TODO Auto-generated method stub

                TextView TxtEmetteur = (TextView) findViewById(R.id.TxtEmetteur);
                TextView text1 = (TextView) findViewById(R.id.text1);
                TextView text2 = (TextView) findViewById(R.id.text2);
                TextView text3 = (TextView) findViewById(R.id.text3);

                if (TxtEmetteur != null) {
                    TxtEmetteur.setText ( NOM_CLIENT );
                }
                if (text1 != null) {
                    text1.setText ( NOM_PRODUIT );
                }
                if (text2 != null) {
                    text2.setText ( REF_PRODUIT );
                }
                if (text3 != null) {
                    text3.setText ( DATE_CREATION );
                }


            }
        });




    }
    ////////////////////////////////////////////////////////////////////////


    ////////////////////////////////////////////////////////////////////////
    public String GetTagRFIDcontent(String urladdress, String _TagRFID) {
        URL url;
        String response = "";

        if (_TagRFID != null){
            urladdress += "?id=";
            urladdress += _TagRFID;
        }
        int SDK_INT = android.os.Build.VERSION.SDK_INT;
        if (SDK_INT > 8)
        {
            StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder()
                    .permitAll().build();
            StrictMode.setThreadPolicy(policy);
            try {
                url = new URL(urladdress);
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("GET");
                conn.setReadTimeout(15001);
                conn.setConnectTimeout(15001);
                conn.setDoInput(true);
                conn.setDoOutput(true);


                int ResponseCode = 200;

                if (ResponseCode == 200) {
                    String line;
                    BufferedReader br = new BufferedReader(new InputStreamReader(conn.getInputStream()));
                    while ((line = br.readLine()) != null) {
                        response += line;
                    }
                } else {
                    response = "";
                }
            } catch (Exception e) {
                e.printStackTrace();
            }

        }
        return response;
    }
    ////////////////////////////////////////////////////////////////////////


    ////////////////////////////////////////////////////////////////////////
    public String GetAllTagRFID(String urladdress, String _streamId)  {
        URL url;
        String response = "";

        if (_streamId != null){
            urladdress += "?apiKey=";
            urladdress += attm2x_API_KEY;
            urladdress += "&deviceId=";
            urladdress += attm2x_DEVICE_ID;
            urladdress += "&streamId=";
            urladdress += _streamId;
        }
        int SDK_INT = android.os.Build.VERSION.SDK_INT;
        if (SDK_INT > 8)
        {
            StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder()
                    .permitAll().build();
            StrictMode.setThreadPolicy(policy);
            try {
                url = new URL(urladdress);
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("GET");
                conn.setReadTimeout(15001);
                conn.setConnectTimeout(15001);
                conn.setDoInput(true);
                conn.setDoOutput(true);


                int ResponseCode = 200;

                if (ResponseCode == 200) {
                    String line;
                    BufferedReader br = new BufferedReader(new InputStreamReader(conn.getInputStream()));
                    while ((line = br.readLine()) != null) {
                        response += line;
                    }
                } else {
                    response = "";
                }
            } catch (Exception e) {
                e.printStackTrace();
            }

        }
        return response;
    }
    ////////////////////////////////////////////////////////////////////////


    ////////////////////////////////////////////////////////////////////////
    public boolean LookNewRFID(String _urlAddress, String _idStream) {
        JSONObject json_response_RFID;
        String _response = GetAllTagRFID(_urlAddress, _idStream);


        try {

            json_response_RFID = new JSONObject(_response);



            // Getting Json ARRAY node
            JSONArray JSON_RFID_values = json_response_RFID.getJSONArray ( RFID_values );
            SimpleDateFormat  format;
            format = new SimpleDateFormat(attm2x_DATE_FORMAT);

            JSONObject JSON_RFID_value = JSON_RFID_values.getJSONObject(0);
            JSONObject c = JSON_RFID_value.getJSONObject("value");
            ///
            Date date_RFID_t = format.parse(c.getString(TIME));
            Long L_date_RFID = date_RFID_t.getTime ();
            ///
            if (last_RFID_TimeStamp < L_date_RFID) {
                last_RFID_TimeStamp = L_date_RFID;
                TAGRFID = c.getString(TAG_VALEURS);
                Thread.sleep ( 100 );
                return true;
            }




        } catch (JSONException | InterruptedException | ParseException e) {
            e.printStackTrace();
        }


        return false;
    }



}


