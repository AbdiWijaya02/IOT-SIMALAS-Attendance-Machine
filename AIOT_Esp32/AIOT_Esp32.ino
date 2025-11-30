#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

#define RX2 16
#define TX2 17
#define DE_RE_PIN 4

const char* ssid = "MANO16";
const char* password = "richard123";
const int MAX_PAKET = 1024;
const char* serverURL = "http://192.168.1.7/AIoT/QuickStart/insertdata/Varbin_data/insertdata.php";

String incomingString = "";
uint8_t packet2[128], packet3[128], packet4[128], packet5[128], packet6[128], packet7[128];
int receivedID = -1;
bool idReceived = false;

 String nama = "";

 String NIM = "";

int stage = 0;

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  Serial2.begin(115200, SERIAL_8N1, RX2, TX2); // RX2 dan TX2 untuk komunikasi dengan Mega
  pinMode(DE_RE_PIN, OUTPUT);
  digitalWrite(DE_RE_PIN, LOW); // Mode receive default

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi connected!");

}

void absen(const String& nim) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    // Ganti URL ini sesuai IP server dan path file PHP kamu
    String url = "http://192.168.1.7/AIoT/QuickStart/insertdata/Varbin_data/Absen.php?nim=" + nim;
    http.begin(url);

    int httpCode = http.GET();
    if (httpCode == 200) {
      String payload = http.getString();
      Serial.println("Response dari server: " + payload);
    } else {
      Serial.println("Gagal kirim absen. Kode HTTP: " + String(httpCode));
    }

    http.end();
  } else {
    Serial.println("WiFi tidak terhubung.");
  }
}

void loop() 
{

 


  if (!idReceived) {
     Serial.flush();
    receiveID();
  } 
  else {
    
    while (receivedID == 999) {
      stage = 1;  
      while (stage == 1) {
        receiveData(); 
       
      }

      while (stage == 2) {
        Serial.println("Stage 2: akucobakirimkemega");
        Serial.println(stage);
        kirimTopMatchKeMega(); // uncomment kalau fungsinya sudah ada


        unsigned long startTime = millis();  // waktu mulai
        unsigned long duration = 2000; 

        
       while (millis() - startTime < duration) {
         
            String command = Serial2.readStringUntil('\n');
            command.trim();  
            Serial.println("Diterima: " + command);
        
            if (command == "REQ_NAMA") {
              Serial.println("Perintah cocok: kirim balasan");
             Serial2.println("NAMA:" + nama + ";NIM:" + NIM);
             absen(NIM);  
              //Serial.println("Mengirim: NIM: " + NIM);
              delay(700);
              Serial.flush();
            }
          

         Serial.flush();
        }
         Serial.flush();
         delay(50);
          stage = 0;
      }
      

    } 
     Serial.flush();
     delay(50);
     receiveData(); 
     stage = 0;
  }

 

  delay(10);
}
void receiveID() {
  while (Serial2.available()) {
    char c = Serial2.read();
    if (c == '\n') {
      if (incomingString.startsWith("ID:")) {
        receivedID = incomingString.substring(3).toInt();
      } else {
        receivedID = incomingString.toInt();
      }
      Serial.print("ID diterima: ");
      Serial.println(receivedID);
      idReceived = true;
      incomingString = "";
    } else {
      incomingString += c;
    }
  }
}
void receiveData() {
  static int index = 0;
  static uint8_t buffer[MAX_PAKET];

  while (Serial2.available()) {
    uint8_t byteData = Serial2.read();
    if (index < MAX_PAKET) {
      buffer[index++] = byteData;
    }
    delayMicroseconds(300);  // Delay antar byte jika perlu
  }

  if (index >= 768) {
    Serial.println("\n--- Data diterima lengkap ---");

    // Tampilkan seluruh data
    Serial.print("uint8_t data[] = { ");
    for (int i = 0; i < index; i++) {
      Serial.print("0x");
      if (buffer[i] < 0x10) Serial.print("0");
      Serial.print(buffer[i], HEX);
      if (i < index - 1) Serial.print(", ");
      if ((i + 1) % 16 == 0) Serial.print("\n                   ");
    }
    Serial.println(" };");

    // Potong jadi 6 paket
    memcpy(packet2, &buffer[0],   128);
    memcpy(packet3, &buffer[130], 128);
    memcpy(packet4, &buffer[260], 128);
    memcpy(packet5, &buffer[390], 128);
    memcpy(packet6, &buffer[520], 128);
    memcpy(packet7, &buffer[640], 128);

    sendToDatabase();


    // Tampilkan isi tiap paket
    for (int pkt = 2; pkt <= 7; pkt++) {
      Serial.print("uint8_t packet"); Serial.print(pkt); Serial.print("[] = {");
      uint8_t* p;
      switch (pkt) {
        case 2: p = packet2; break;
        case 3: p = packet3; break;
        case 4: p = packet4; break;
        case 5: p = packet5; break;
        case 6: p = packet6; break;
        case 7: p = packet7; break;
      }
      for (int i = 0; i < 128; i++) {
        Serial.print("0x");
        printHex(p[i], 2);
        if (i < 127) Serial.print(", ");
      }
      Serial.println(" };");

       
    }
    stage += 1;
    // Reset buffer dan status
    index = 0;
    idReceived = false;
    receivedID = -1;
  } else if (index > 0) {
    Serial.print("📦 Byte diterima sementara: ");
    Serial.println(index);
  }
}
void sendToDatabase() {
  HTTPClient http;
  http.begin(serverURL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String postData = "user_id=" + String(receivedID);

  // Konversi tiap packet ke string hex dan tambahkan ke postData
  // Packet 2
  postData += "&packet2_hex=";
  for (int i = 0; i < 128; i++) {
    if (packet2[i] < 0x10) postData += "0";  // Padding '0' jika nilai < 16 (0x10)
    postData += String(packet2[i], HEX);
  }

  // Packet 3
  postData += "&packet3_hex=";
  for (int i = 0; i < 128; i++) {
    if (packet3[i] < 0x10) postData += "0";
    postData += String(packet3[i], HEX);
  }

  // Packet 4
  postData += "&packet4_hex=";
  for (int i = 0; i < 128; i++) {
    if (packet4[i] < 0x10) postData += "0";
    postData += String(packet4[i], HEX);
  }

  // Packet 5
  postData += "&packet5_hex=";
  for (int i = 0; i < 128; i++) {
    if (packet5[i] < 0x10) postData += "0";
    postData += String(packet5[i], HEX);
  }

  // Packet 6
  postData += "&packet6_hex=";
  for (int i = 0; i < 128; i++) {
    if (packet6[i] < 0x10) postData += "0";
    postData += String(packet6[i], HEX);
  }

  // Packet 7
  postData += "&packet7_hex=";
  for (int i = 0; i < 128; i++) {
    if (packet7[i] < 0x10) postData += "0";
    postData += String(packet7[i], HEX);
  }

  // Kirim ke server
  int httpResponseCode = http.POST(postData);
  Serial.print("Response Code: ");
  Serial.println(httpResponseCode);


  String payload = http.getString();
  Serial.println("Server Response: " + payload);
  http.end();
}


// Fungsi bantu cetak hex rapi
void printHex(int num, int precision) {
  char tmp[16];
  char format[16];
  sprintf(format, "%%0%dX", precision);
  sprintf(tmp, format, num);
  Serial.print(tmp);
}

void kirimTopMatchKeMega() {
  HTTPClient http;
  http.begin("http://192.168.1.7/AIoT/QuickStart/insertdata/Varbin_data/Voting.php?topscore=1");

  int httpCode = http.GET();

  if (httpCode == 200) {
    String payload = http.getString();
    Serial.println("Top Match Data:");
    Serial.println(payload);

    DynamicJsonDocument doc(2048);
    DeserializationError error = deserializeJson(doc, payload);
    if (error) {
      Serial.print("JSON parsing error: ");
      Serial.println(error.c_str());
      http.end();
      return;
    }
    nama = doc["nama"].as<String>();
    NIM =  doc["NIM"].as<String>();
    String hexString = doc["template_content"];
   

    
    const int totalBytes = hexString.length() / 2;
    const int packetSize = 128;
    const int packetCount = totalBytes / packetSize;
    uint8_t fullBytes[768];

    for (int i = 0; i < totalBytes; i++) {
      String byteStr = hexString.substring(i * 2, i * 2 + 2);
      fullBytes[i] = (uint8_t) strtol(byteStr.c_str(), NULL, 16);
    }

    for (int p = 0; p < packetCount; p++) {
    Serial.printf("uint8_t packet%d[] = {", p + 1);
    for (int i = 0; i < packetSize; i++) {
      Serial.printf("0x%02X", fullBytes[p * packetSize + i]);
      if (i < packetSize - 1) Serial.print(", ");
    }
    Serial.println("};\n");
  }


    // Kirim ke Mega via Serial2
    digitalWrite(DE_RE_PIN, HIGH); // Aktifkan mode TX
    delay(2);
    Serial2.write(fullBytes, 768);
    delay(10); // Tunggu hingga pengiriman selesai;

    
    digitalWrite(DE_RE_PIN, LOW); // Kembali ke RX
    delay(10);
    


  } else {
    Serial.println("Failed to fetch top match");
  }
  http.end();
}
