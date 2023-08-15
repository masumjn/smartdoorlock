#include <EEPROM.h>
#include <WiFi.h>
#include <HTTPClient.h>

const byte eepromValid = 123;    

const int programButton = 21;   
const int ledPinG = 12;          
const int ledPinR = 14; 
const int knockSensor = 34; 
//const int knockSensor = 27;    
const int audioOut = 27;        
const int lockPin = 26;        
const int miniFan = 15;
 

int threshold = 3;               
const int rejectValue = 25;        
const int averageRejectValue = 15; 
const int knockFadeTime = 150;     
const int lockOperateTime = 2500;  
const int maximumKnocks = 20;      
const int knockComplete = 1200;    
 
byte secretCode[maximumKnocks] = {50, 25, 25, 50, 100, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0};  // Initial setup: "Shave and a Hair Cut, two bits."
int knockReadings[maximumKnocks];    
int knockSensorValue = 0;            
boolean programModeActive = false;   

const char* ssid = "OneOnly";
const char* password = "11111111";

String serverName = "http://192.168.43.61/smartdoorlock/";

unsigned long lastTime = 0;
unsigned long timerDelay = 500;
String payload;
int countCheck = 0;

void setup() {
  Serial.begin(9600); 
  pinMode(audioOut, OUTPUT);
  pinMode(ledPinR, OUTPUT);
  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    digitalWrite(ledPinR, HIGH);
    delay(250);
    digitalWrite(ledPinR, LOW);
    delay(200);
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
  Serial.println("Timer set to 5 seconds (timerDelay variable), it will take 5 seconds before publishing the first reading.");
  
  tone(audioOut, 2093.00, 150);
  tone(audioOut, 0, 10);
  tone(audioOut, 2637.02, 100);
  tone(audioOut, 0, 10);
  tone(audioOut, 4186.01, 200);
//  tone(audioOut, 0, 50);
  digitalWrite(audioOut, LOW);
  delay(500);
  
  pinMode(programButton,INPUT_PULLUP);
  pinMode(ledPinG, OUTPUT); 
  pinMode(lockPin, OUTPUT); 
  pinMode(knockSensor, OUTPUT);
  pinMode(miniFan, OUTPUT);
  digitalWrite(ledPinG, HIGH);
  digitalWrite(ledPinR, HIGH);
  delay(500);
  digitalWrite(ledPinG, LOW);
  digitalWrite(ledPinR, LOW); 
  readSecretKnock();   
  doorUnlock0(500);    
  digitalWrite(ledPinG, HIGH);
}
 
void loop() {
 
  knockSensorValue = analogRead(knockSensor);
  if (digitalRead(programButton) == LOW){
    delay(100);  
    if (digitalRead(programButton) == LOW){        
      if (programModeActive == false){     
        programModeActive = true;          
        digitalWrite(ledPinG, HIGH);    
        digitalWrite(ledPinR, HIGH);   
        chirp(500, 1000);                  
//        chirp(500, 800);
      } else {                             
        programModeActive = false;
        digitalWrite(ledPinG, HIGH); 
        digitalWrite(ledPinR, LOW);
//        chirp(500, 800);                  
        chirp(500, 1000);
        delay(500);
      }
      while (digitalRead(programButton) == LOW){
//        delay(10);                        
      } 
    }
    delay(250);   
  }
  
  
  if (knockSensorValue >= threshold){
     if (programModeActive == true){  
       digitalWrite(ledPinG, LOW);
     } else {
       digitalWrite(ledPinG, HIGH);
     }
     knockDelay();
     if (programModeActive == true){  
       digitalWrite(ledPinG, HIGH);
       digitalWrite(ledPinR, HIGH);
     } else {       
       digitalWrite(ledPinG, HIGH);
       digitalWrite(ledPinR, LOW);
     }
     listenToSecretKnock();           
  }


  //Send an HTTP POST request every 10 minutes
  if ((millis() - lastTime) > timerDelay) {
    //Check WiFi connection status
    if(WiFi.status()== WL_CONNECTED){
      HTTPClient http;
      String serverPath = serverName + "cekRelay.php";
      
      // Your Domain name with URL path or IP address with path
      http.begin(serverPath.c_str());
      
      // If you need Node-RED/server authentication, insert user and password below
      //http.setAuthorization("REPLACE_WITH_SERVER_USERNAME", "REPLACE_WITH_SERVER_PASSWORD");
      
      // Send HTTP GET request
      int httpResponseCode = http.GET();
      
      if (httpResponseCode>0) {
//        Serial.print("HTTP Response code: ");
//        Serial.println(httpResponseCode);
        payload = http.getString();        
        Serial.println(payload);
        http.end();
        if(payload == "1"){
          if (countCheck<1){
            digitalWrite(lockPin, HIGH);
            delay(500);
            digitalWrite(audioOut, HIGH);
            delay(200);
            digitalWrite(audioOut, LOW);
            
            WiFiClient klien;
            String sendData1 = serverName + "tambahHistory.php?status=" + String(payload);
            http.begin(klien, sendData1);
            http.GET();
            String respon = http.getString();
            Serial.println(respon);
            http.end();
            
            delay(5000);            
            digitalWrite(lockPin, LOW);
            
          }
          countCheck += 1;
          digitalWrite(miniFan, HIGH);
          
        } else {
           
          if (countCheck>1){ 
            digitalWrite(lockPin, LOW);
            delay(500);
            digitalWrite(audioOut, HIGH);
            delay(200);
            digitalWrite(audioOut, LOW);
            
            WiFiClient klien;
            String sendData2 = serverName + "tambahHistory.php?status=" + String(payload);
            http.begin(klien, sendData2);
            http.GET();
            String respon = http.getString();
            Serial.println(respon);
            http.end();     
            
            
            countCheck = 0;
          }
          digitalWrite(miniFan, LOW);
        }
      }
      else {
        Serial.print("Error code: ");
        Serial.println(httpResponseCode);
      }
    }
    else {
      Serial.println("WiFi Disconnected");
    }
    lastTime = millis();
  }
} 
 

void listenToSecretKnock(){
  int i = 0;
  
  for (i=0; i < maximumKnocks; i++){
    knockReadings[i] = 0;
  }
  
  int currentKnockNumber = 0;             
  int startTime = millis();                
  int now = millis();   
 
  do {                                     
    knockSensorValue = analogRead(knockSensor);
    if (knockSensorValue >= threshold){                  
      now=millis();
      knockReadings[currentKnockNumber] = now - startTime;
      currentKnockNumber ++;                             
      startTime = now;   
      
      digitalWrite(ledPinG, LOW);       
      if (programModeActive==true){     
        digitalWrite(ledPinG, LOW);
      } else {
        digitalWrite(ledPinG, HIGH);
      } 
//      knockDelay();     
      delay(150);
      digitalWrite(ledPinG, HIGH);
      if (programModeActive == true){
        digitalWrite(ledPinG, HIGH);
      } else {
        digitalWrite(ledPinG, LOW);
      }
   }
 
    now = millis();
  
   
  } while ((now-startTime < knockComplete) && (currentKnockNumber < maximumKnocks));
  
 
  if (programModeActive == false){         
    if (validateKnock() == true){
      doorUnlock(lockOperateTime);
      digitalWrite(ledPinG, HIGH);
    } else {      
      for (i=0; i < 4; i++){          
        digitalWrite(ledPinG, HIGH);
        delay(50);
        digitalWrite(ledPinG, LOW);
        delay(50);
      }
      
      digitalWrite(ledPinG, LOW);    
      for (i=0;i<3;i++){          
        digitalWrite(ledPinR, HIGH);
        digitalWrite(audioOut, LOW);
        delay(100);
        digitalWrite(ledPinR, LOW);
        digitalWrite(audioOut, HIGH);
        delay(100);
      }      
      digitalWrite(ledPinG, HIGH);
      digitalWrite(audioOut, LOW);
    }
  } else { 
    validateKnock();
  }
}
 
 

void doorUnlock0(int delayTime){
  digitalWrite(audioOut, LOW);
  digitalWrite(ledPinG, HIGH);
  digitalWrite(ledPinR, HIGH);
  digitalWrite(lockPin, HIGH);
  digitalWrite(audioOut, HIGH);
  delay(200);
  digitalWrite(audioOut, LOW);
  delay(delayTime);
  digitalWrite(lockPin, LOW);
  digitalWrite(ledPinG, LOW);
  digitalWrite(ledPinR, LOW); 
  delay(500);   
}

void doorUnlock(int delayTime){
  digitalWrite(ledPinG, HIGH);
  digitalWrite(ledPinR, HIGH);
  digitalWrite(lockPin, HIGH);
  digitalWrite(audioOut, HIGH);
  delay(200);
  digitalWrite(audioOut, LOW);
  delay(delayTime);

  HTTPClient http;
  WiFiClient klien;
  String sendData1 = serverName + "tambahHistory.php?status=1";
  http.begin(klien, sendData1);
  http.GET();
  String respon = http.getString();
  Serial.println(respon);
  http.end();
  
  delay(200);
  digitalWrite(lockPin, LOW);
  digitalWrite(ledPinG, LOW);
  digitalWrite(ledPinR, LOW); 

  String sendData2 = serverName + "tambahHistory.php?status=2";
  http.begin(klien, sendData2);
  http.GET();
  respon = http.getString();
  Serial.println(respon);
  http.end();
  delay(500);

}

boolean validateKnock(){
  int i = 0;
 
  int currentKnockCount = 0;
  int secretKnockCount = 0;
  int maxKnockInterval = 0;             
  
  for (i=0;i<maximumKnocks;i++){
    if (knockReadings[i] > 0){
      currentKnockCount++;
    }
    if (secretCode[i] > 0){         
      secretKnockCount++;
    }
    
    if (knockReadings[i] > maxKnockInterval){   
      maxKnockInterval = knockReadings[i];
    }
  }
  
  if (programModeActive == true){
      for (i=0; i < maximumKnocks; i++){ 
        secretCode[i] = map(knockReadings[i], 0, maxKnockInterval, 0, 100); 
      }
      saveSecretKnock();               
      programModeActive = false;
      playbackKnock(maxKnockInterval);  
      for (i=0;i<3;i++){
        delay(100);
        digitalWrite(ledPinR, HIGH);
        digitalWrite(ledPinG, LOW);
        delay(100);
        digitalWrite(ledPinR, LOW);
        digitalWrite(ledPinG, HIGH);      
      }
      return false;
  }
  
  if (currentKnockCount != secretKnockCount){  
    return false;
  }
  
  int totaltimeDifferences = 0;
  int timeDiff = 0;
  for (i=0; i < maximumKnocks; i++){  
    knockReadings[i]= map(knockReadings[i], 0, maxKnockInterval, 0, 100);      
    timeDiff = abs(knockReadings[i] - secretCode[i]);
    if (timeDiff > rejectValue){       
      return false;
    }
    totaltimeDifferences += timeDiff;
  }
  
  if (totaltimeDifferences / secretKnockCount > averageRejectValue){
    return false; 
  }
  
  return true;
}
 
void readSecretKnock(){
  byte reading;
  int i;
  reading = EEPROM.read(0);
  if (reading == eepromValid){   
    for (int i=0; i < maximumKnocks ;i++){
      secretCode[i] =  EEPROM.read(i+1);
    }
  }
}
 
 
void saveSecretKnock(){
  EEPROM.write(0, 0);  
  for (int i=0; i < maximumKnocks; i++){
    EEPROM.write(i+1, secretCode[i]);
  }
  EEPROM.write(0, eepromValid);  
}
 
void playbackKnock(int maxKnockInterval){
      digitalWrite(ledPinG, LOW);
      digitalWrite(ledPinR, LOW);
      delay(1000);
      digitalWrite(ledPinG, HIGH);
      digitalWrite(ledPinR, HIGH);
      chirp(200, 1800);
      for (int i = 0; i < maximumKnocks ; i++){
        digitalWrite(ledPinG, LOW);
        digitalWrite(ledPinR, LOW);
       
        if (secretCode[i] > 0){                                   
          delay(map(secretCode[i], 0, 100, 0, maxKnockInterval)); 
          digitalWrite(ledPinG, HIGH);
          digitalWrite(ledPinR, HIGH);
          chirp(200, 1800);
        }
      }
      delay(300);
      digitalWrite(ledPinG, HIGH);
      digitalWrite(ledPinR, LOW);
}
 
void knockDelay(){
  int itterations = (knockFadeTime / 20);      
  for (int i=0; i < itterations; i++){
    delay(10);
    analogRead(knockSensor);                  
    delay(10);
  } 
}
 
void chirp(int playTime, int delayTime){
  long loopTime = (playTime * 1000L) / delayTime;
  pinMode(audioOut, OUTPUT);
  for(int i=0; i < loopTime; i++){
    digitalWrite(audioOut, HIGH);
    delayMicroseconds(delayTime);
    digitalWrite(audioOut, LOW);
  }
}
