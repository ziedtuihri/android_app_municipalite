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
            @Field("email") String email,
            @Field("password") String password,
            @Field("confirm_password") String confirm_password,
            @Field("address") String address,
            @Field("city") String city,
            @Field("tel") String tel,
            @Field("cin") String cin
    );

}
