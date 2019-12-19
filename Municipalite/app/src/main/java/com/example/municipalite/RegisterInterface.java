package com.example.municipalite;

import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;


public interface RegisterInterface {

    String REGIURL = "http://196.229.46.157/municipalite2/public/api/";
    @FormUrlEncoded
    @POST("register")
    Call<String> getUserRegi(
            @Field("name") String name,
            @Field("hobby") String hobby
    );

}
