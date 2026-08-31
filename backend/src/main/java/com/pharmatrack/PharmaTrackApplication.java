package com.pharmatrack;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.scheduling.annotation.EnableScheduling;

@SpringBootApplication
@EnableScheduling
public class PharmaTrackApplication {

    public static void main(String[] args) {
        SpringApplication.run(PharmaTrackApplication.class, args);
    }
}
