package com.example.municipalite;

public class ModelListView {

    private String name, adress, city, imgURL, dateD, dateF, HeurD, HeurF, description;

    public void setName(String name) {
        this.name = name;
    }

    public void setAdress(String adress) {
        this.adress = adress;
    }

    public void setCity(String city) {
        this.city = city;
    }

    public void setImgURL(String imgURL) {
        this.imgURL = MyInterface.img+imgURL;
    }

    public void setDateD(String dateD) {
        this.dateD = dateD;
    }

    public void setDateF(String dateF) {
        this.dateF = dateF;
    }

    public void setHeurD(String heurD) {
        HeurD = heurD;
    }

    public void setHeurF(String heurF) {
        HeurF = heurF;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getName() {
        return name;
    }

    public String getAdress() {
        return adress;
    }

    public String getCity() {
        return city;
    }

    public String getImgURL() {
        return imgURL;
    }

    public String getDateD() {
        return dateD;
    }

    public String getDateF() {
        return dateF;
    }

    public String getHeurD() {
        return HeurD;
    }

    public String getHeurF() {
        return HeurF;
    }

    public String getDescription() {
        return description;
    }
}
