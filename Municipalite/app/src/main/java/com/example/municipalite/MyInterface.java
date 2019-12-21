package com.example.municipalite;

import retrofit2.Call;
import retrofit2.http.GET;

public interface MyInterface {

    String JSONURL = "http://196.234.17.237/municipalite2/public/api/";
    //String JSONURL ="https://demonuts.com/Demonuts/JsonTest/Tennis/json_parsing.php";
    String img = "http://196.234.17.237/municipalite2/image/";

    @GET("articles")
    Call<String> getString();
}