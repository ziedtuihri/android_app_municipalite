package com.example.municipalite;

import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

public interface LoginInterface {

        String LOGINURL = "http://196.229.46.157/municipalite2/public/api/";
    @FormUrlEncoded
    @POST("login")
    Call<String> getUserLogin(

            @Field("email") String email,
            @Field("password") String password
    );

}
