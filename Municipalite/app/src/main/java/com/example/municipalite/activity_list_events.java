package com.example.municipalite;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.widget.ListView;
import android.widget.Toast;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.scalars.ScalarsConverterFactory;

public class activity_list_events extends AppCompatActivity {
    private ListView listView;
    private RetroAdapter retroAdapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_list_events);

        Intent intent = getIntent();

        listView = findViewById(R.id.lv);
        getJSONResponse();
    }

    private void getJSONResponse(){
        Retrofit retrofit = new Retrofit.Builder()
                .baseUrl(MyInterface.JSONURL)
                .addConverterFactory(ScalarsConverterFactory.create())
                .build();

        MyInterface api = retrofit.create(MyInterface.class);

        Call<String> call = api.getString();

        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.i("Responsestring", response.body().toString());
                //Toast.makeText()
                if (response.isSuccessful()) {
                    if (response.body() != null) {
                        Log.i("onSuccess", response.body().toString());

                        String jsonresponse = response.body().toString();
                        writeListView(jsonresponse);

                    } else {
                        Log.i("onEmptyResponse", "Returned empty response");//Toast.makeText(getContext(),"Nothing returned",Toast.LENGTH_LONG).show();
                    }
                }
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Toast.makeText(activity_list_events.this,"Server invalid \n"+t.getMessage(),Toast.LENGTH_LONG).show();
            }
        });
    }


    private void writeListView(String response){

        try {
            //getting the whole json object from the response
            JSONObject obj = new JSONObject(response);
            if(obj.optString("success").equals("true")){
                Toast.makeText(activity_list_events.this, "Do Login Successfully!", Toast.LENGTH_SHORT)
                        .show();

                ArrayList<ModelListView> modelListViewArrayList = new ArrayList<>();
                JSONArray dataArray  = obj.getJSONArray("data");

                for (int i = 0; i < dataArray.length(); i++) {

                    ModelListView modelListView = new ModelListView();
                    JSONObject dataobj = dataArray.getJSONObject(i);

                    modelListView.setImgURL(dataobj.getString("image"));
                    modelListView.setName(dataobj.getString("title"));
                    modelListView.setAdress(dataobj.getString("region"));
                    modelListView.setCity(dataobj.getString("adress"));
                    modelListView.setDateD(dataobj.getString("date_debut"));
                    modelListView.setHeurD(dataobj.getString("Heur_debut"));
                    modelListView.setHeurF(dataobj.getString("Heur_fin"));
                    modelListView.setDescription(dataobj.getString("mini_description"));

                    modelListViewArrayList.add(modelListView);

                }

                retroAdapter = new RetroAdapter(this, modelListViewArrayList);
                listView.setAdapter(retroAdapter);

            }else {
                Toast.makeText(activity_list_events.this, obj.optString("message")+"", Toast.LENGTH_SHORT).show();
            }

        } catch (JSONException e) {
            e.printStackTrace();
        }

    }

}
