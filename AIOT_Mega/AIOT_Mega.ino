#include <Adafruit_Fingerprint.h>
#define DATA_LENGTH 768
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&Serial3);

int p;
int index = 0;
int stage = 0;
int count = 0;
int user_id;

uint8_t id;
uint8_t mode;
uint8_t receivedData[DATA_LENGTH];
uint8_t apacket2[128], apacket3[128], apacket4[128], apacket5[128], apacket6[128], apacket7[128];
uint8_t packet2[128], packet3[128], packet4[128], packet5[128], packet6[128], packet7[128];

String namaDariESP = "";
String nimDariESP = "";

const size_t packet2_len = sizeof(packet2);
const size_t packet3_len = sizeof(packet3);
const size_t packet4_len = sizeof(packet4);
const size_t packet5_len = sizeof(packet5);
const size_t packet6_len = sizeof(packet6);
const size_t packet7_len = sizeof(packet7);

void sendPacket(const uint8_t* packet, size_t length) {
    for (size_t i = 0; i < length; i++) {
      Serial1.write(packet[i]);
    }
    Serial1.println(); // opsional, hanya untuk jeda baris
}

void sendRequestNama() {
  Serial1.println("REQ_NAMA"); // Kirim permintaan nama dalam bentuk string
}


void send_userid(int user_id) {
  Serial1.print("ID:");
  Serial1.println(user_id);
}

void setup() {
  Serial.begin(115200);
  Serial1.begin(115200);  // Data masuk dari ESP32
  finger.begin(57600);
  
  if (finger.verifyPassword()) {
    Serial.println("Fingerprint sensor detected.");
  } else {
    Serial.println("Fingerprint sensor NOT detected!");
    while (1);
  }
}
uint8_t readnumber(void) {
    uint8_t num = 0;
    
    while (num == 0) {
      while (! Serial.available());
      num = Serial.parseInt();
    }
    return num;
}
uint8_t readmode(void) {
    uint8_t mode = 0;
    
    while (mode == 0) {
      while (! Serial.available());
      mode = Serial.parseInt();
    }
    return mode;
}

uint8_t read_id(void) {
    uint8_t user_id = 0;
    while (user_id == 0) {
      while (!Serial.available()); // Tunggu input
      user_id = Serial.parseInt(); // Konversi teks "123" → 123 (int)
    }
    return user_id;
}



void terimaData() {
  while (Serial1.available() > 0 && index < DATA_LENGTH) {
    receivedData[index++] = Serial1.read();
  }

  if (index == DATA_LENGTH) {
    // Langsung pisahkan ke packet saat data lengkap
    memcpy(apacket2, &receivedData[0],   128);
    memcpy(apacket3, &receivedData[128], 128);
    memcpy(apacket4, &receivedData[256], 128);
    memcpy(apacket5, &receivedData[384], 128);
    memcpy(apacket6, &receivedData[512], 128);
    memcpy(apacket7, &receivedData[640], 128);
  }
}

void printPackets() {
  Serial.println("=== Data template diterima dan dipisah ===");
  
    for (int p = 0; p < 6; p++) {
      Serial.print("uint8_t packet");
      Serial.print(p + 1);
      Serial.print("[] = {");
    
      for (int i = 0; i < 128; i++) {
        uint8_t val = receivedData[p * 128 + i];
    
        Serial.print("0x");
        if (val < 0x10) Serial.print("0");  // Leading zero
        Serial.print(val, HEX);
    
        if (i < 127) Serial.print(", ");
      }
    
      Serial.println("};\n");
    }
}



int getFingerprintIDez() {

unsigned long startTime = millis();  // waktu mulai
unsigned long duration = 2000;

bool reqNamaSent = false;

  Serial.println("Langsung mencocokkan fingerprint hasil upload...");
 

  // Langsung coba match fingerprint yang sudah di-upload (template harus sudah dikirim sebelumnya)
  p = finger.matchUpload();  
  if (p != FINGERPRINT_OK) {
    Serial.println("\t ===??? [ DOESN'T Match ]");
    delay(500);
    Serial.flush(); 
     finger.emptyDatabase();
     stage = 0;
     
    
    return -1;  // fingerprint doesn't match
  } else {
    
       while (millis() - startTime < duration) {
          if (!reqNamaSent) {
              Serial1.println("REQ_NAMA");
              reqNamaSent = true;
            }
         if (reqNamaSent && millis() - startTime < duration) {
            if (Serial1.available()) {
              String response = Serial1.readStringUntil('\n');
              response.trim();  // hapus \r dan spasi
              Serial.println("\t ===>>> [ MATCH !!! ]");
              Serial.println("\t ===>>> [" + response + "]");  // Debug lihat isi sebenarnya
              delay(500);
              // Gunakan toUpperCase kalau perlu case-insensitive
            if (response.startsWith("NAMA:") && response.indexOf("NIM:") > 0) {
                  int indexNIM = response.indexOf("NIM:");
                  
                  // Ambil bagian nama
                  namaDariESP = response.substring(5, indexNIM - 1); // -1 untuk hapus tanda ';'
                  namaDariESP.trim();
                
                  // Ambil bagian NIM
                  nimDariESP = response.substring(indexNIM + 4); // setelah "NIM:"
                  nimDariESP.trim();
                
                  Serial.println("Nama diterima dari ESP32: " + namaDariESP);
                  Serial.println("NIM  diterima dari ESP32: " + nimDariESP);
                
                  reqNamaSent = false;
                  delay(700);
                }
                Serial.flush(); 
              
            }
          }
        }
         Serial.flush(); 
        reqNamaSent = false;
      
       Serial.flush();  
       delay(500);
      
  }   
  delay(500);
    finger.emptyDatabase();



  return finger.fingerID; 
  delay(700);
}
bool uploaded = false;


void loop() {

  id = 1;
  Serial.println("Silakan ketik mode:");
  Serial.println("1 = Enroll fingerprint");
  Serial.println("2 = Verifikasi fingerprint");
  Serial.println("...........................................................");
  mode = readmode();
   if (mode == 1) {
    Serial.println("Please type ID Number :");
    user_id = read_id();
    if(user_id >= 0){
        send_userid(user_id); 
        delay(1000);
        Serial.println("Ready to Show Fingerprint!");
        Serial.println("Please type Free Number :");
        Serial.print("\n[Enroll ID #");
        Serial.print(id);
          if (id == 0) {
            // ID #0 tidak diperbolehkan
            return;
          }
            Serial.print("Show  ID #");
            Serial.println(id);    
            getFingerprintEnroll();
            delay(50);
            Serial.flush(); 
             delay(50);
            count += 1;
            stage = 0;
    }
  }

  else{
    user_id = 999;
    send_userid(user_id);
        while(stage == 0){
        getFingerprintSingleTap();
        }
          while(stage > 0){
          terimaData();
            if (index == DATA_LENGTH) {
              printPackets();
              index = 0;
                 while (!uploaded) {
                  Serial.println("Mulai upload...");
                  int result = finger.uploadModel2(apacket2, apacket3, apacket4, apacket5, apacket6, apacket7);  
                  delay(100);
                  uploaded = true; // supaya tidak upload terus
                  }
                  getFingerprintIDez();
                  
                   stage = 0;
                   finger.emptyDatabase();
                   uploaded = false;
                   
            }
          }
        }
}

uint8_t getFingerprintEnroll() {
  Serial.flush();  // Membersihkan buffer dari sensor fingerprint
  p = -1;
  Serial.print("Waiting for valid finger to enroll as #"); Serial.println(id);
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
    case FINGERPRINT_OK:
      Serial.println("\nImage taken");
      break;
    case FINGERPRINT_NOFINGER:
      Serial.print(",");
      break;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      break;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      break;
    default:
      Serial.println("Unknown error");
      break;
    }
  }

  // OK success!

  p = finger.image2Tz(1);
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
     
      downloadFingerprintTemplate(id);
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }
  
  Serial.print("Remove finger ");
  delay(2000);
  p = 0;
  while (p != FINGERPRINT_NOFINGER) {
    p = finger.getImage();
  }
  Serial.print("ID "); Serial.println(id);
  p = -1;
  Serial.println("Place same finger again");
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
    case FINGERPRINT_OK:
      Serial.println("\nImage taken");
      break;
    case FINGERPRINT_NOFINGER:
      Serial.print(".");
      break;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      break;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      break;
    default:
      Serial.println("Unknown error");
      break;
    }
  }
  
  // OK success!

  p = finger.image2Tz(2);
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
       send_userid(user_id); 
      downloadFingerprintTemplate(id);
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }
  
  // OK converted!
  Serial.print("Creating model for #");  Serial.println(id);
  
  p = finger.createModel();
  if (p == FINGERPRINT_OK) {
    Serial.println("Prints matched!");
  p = 1;
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_ENROLLMISMATCH) {
    Serial.println("Fingerprints did not match");
    return p;
  } else {
    Serial.println("Unknown error");
    return p;
  }

    delay(500);
    send_userid(user_id); 
    delay(500);
    downloadFingerprintTemplate(id); 
    delay(50);
    finger.emptyDatabase();
    delay(50);
}
uint8_t downloadFingerprintTemplate(uint16_t id)
{      
  Serial.print("==> Attempting to get Templete #"); Serial.println(id);
  p = finger.getModel();
  switch (p) {
    case FINGERPRINT_OK:
      Serial.print("Template "); Serial.print(id); Serial.println(" transferring:");
      break;
   default:
      Serial.print("Unknown error "); Serial.println(p);
      return p;
  }

  uint8_t bytesReceived[900];

  int i = 0;
  while (i <= 900 ) { 
      if (Serial3.available()) {
          bytesReceived[i++] = Serial3.read();
      }
  }
  
  Serial.println("Decoding packet...");
  
  // Filtering The Packet
  int a = 0, x = 3;;
  Serial.print("uint8_t packet2[] = {");
  for (int i = 10; i <= 832; ++i) {
      a++;
      if (a >= 129)
        {
          i+=10;
          a=0;
          Serial.println("};");Serial.print("uint8_t packet");Serial.print(x);Serial.print("[] = {");
      x++;
        }
      else
      {
         Serial.print("0x"); printHex(bytesReceived[i-1] , 2); Serial.print(", ");
      }
  }
  // Simpan data ke array internal
  memcpy(packet2, &bytesReceived[9],   128);
  memcpy(packet3, &bytesReceived[148], 128);
  memcpy(packet4, &bytesReceived[288], 128);
  memcpy(packet5, &bytesReceived[426], 128);
  memcpy(packet6, &bytesReceived[565], 128);
  memcpy(packet7, &bytesReceived[704], 128);



  Serial.println("};");
  Serial.println("COMPLETED\n");

  Serial.println("Verifikasi penyimpanan ke variabel:");

  sendPacket(packet2, packet2_len);
  delay(50); // jeda antar packet

  sendPacket(packet3, packet3_len);
  delay(50);

  sendPacket(packet4, packet4_len);
  delay(50);

  sendPacket(packet5, packet5_len);
  delay(50);
  
  sendPacket(packet6, packet6_len);
  delay(50);
  
  sendPacket(packet7, packet7_len);
  delay(50); // tunggu sebelum loop ulang
  
   stage += 1;
   Serial.print(stage);
}


uint8_t getFingerprintSingleTap() {
  deleteFingerprint(id);
  p = -1;
  Serial.println("Place your finger on the sensor...");
  
  // 1. Ambil gambar sidik jari (satu kali tap)
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
      case FINGERPRINT_OK:
        Serial.println("Image taken");
        break;
      case FINGERPRINT_NOFINGER:
        Serial.print(".");
        break;
      default:
        Serial.println("Error, try again");
        return p;
    }
  }

  // 2. Konversi ke template (tanpa tap kedua)
  p = finger.image2Tz(1); // Gunakan slot 1 (bukan enroll, jadi tidak perlu slot 2)
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Template extracted");
      break;
    default:
      Serial.println("Error converting image");
      return p;
  }

  // 3. Langsung download template tanpa enroll
  downloadFingerprintTemplate(id);
  delay(50);
  finger.emptyDatabase();
    delay(50);
  return p;
}

void printHex(int num, int precision) {
    char tmp[16];
    char format[128];
 
    sprintf(format, "%%.%dX", precision);
 
    sprintf(tmp, format, num);
    Serial.print(tmp);
}




uint8_t deleteFingerprint(uint16_t id) {
  uint8_t p = finger.deleteModel(id);
  if (p == FINGERPRINT_OK) {
    Serial.print("Fingerprint ID ");
    Serial.print(id);
    Serial.println(" berhasil dihapus");
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Error komunikasi saat menghapus fingerprint");
  } else if (p == FINGERPRINT_BADLOCATION) {
    Serial.println("ID fingerprint tidak valid");
  } else if (p == FINGERPRINT_FLASHERR) {
    Serial.println("Error memory sensor saat menghapus fingerprint");
  } else {
    Serial.println("Error tidak diketahui saat menghapus fingerprint");
  }
  return p;
}
