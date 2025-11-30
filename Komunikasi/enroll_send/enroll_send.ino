#include <Adafruit_Fingerprint.h>

// Serial3    ==> Tx (White) = 14   ==> Rx (Green) = 15
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&Serial3);

uint8_t id;
int p;

// Global array untuk simpan hasil template
uint8_t packet2[128], packet3[128], packet4[128], packet5[128], packet6[128], packet7[128];


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




void setup()  
{
  Serial.begin(115200);
  while (!Serial);
  delay(100);
  
  Serial1.begin(115200); // Atur baud rate sesuai kebutuhanmu
  delay(100); // Tunggu Serial1 ready (opsional)
  
  
  Serial.println("\n[ Enroll + Show Templete ]\n");

  // set the data rate for the sensor serial port
  // HIGH baudrate must use Hardware Serial (Serial3 / Serial2 / Serial1)
  finger.begin(57600);
  
  if (finger.verifyPassword()) {
    Serial.println("Found fingerprint sensor!");
  } else {
    Serial.println("Did not find fingerprint sensor :(");
    while (1) { delay(1); }
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

void loop()                     // run over and over again
{
  Serial.println("Ready to Show Fingerprint!");
  Serial.println("Please type Free Number :");
  id = readnumber();
  if (id == 0) {// ID #0 not allowed, try again!
     return;
  }
  Serial.print("Show  ID #");
  Serial.println(id);
  
  getFingerprintEnroll();
}

uint8_t getFingerprintEnroll() {

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

    downloadFingerprintTemplate(id); 
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
  memcpy(packet3, &bytesReceived[148],   128);
  memcpy(packet4, &bytesReceived[288], 128);
  memcpy(packet5, &bytesReceived[426], 128);
  memcpy(packet6, &bytesReceived[565], 128);
  memcpy(packet7, &bytesReceived[704], 128);



  Serial.println("};");
  Serial.println("COMPLETED\n");

  Serial.println("Verifikasi penyimpanan ke variabel:");

 // Tampilkan semua isi dari packet2 - packet7
for (int pkt = 2; pkt <= 7; pkt++) {
  Serial.print("uint8_t packet"); Serial.print(pkt); Serial.print("[] = {");
  uint8_t* p;
  switch(pkt) {
    case 2: p = packet2; break;
    case 3: p = packet3; break;
    case 4: p = packet4; break;
    case 5: p = packet5; break;
    case 6: p = packet6; break;
    case 7: p = packet7; break;
  }
  for (int i = 0; i < 128; i++) {
    Serial.print("0x"); printHex(p[i], 2); Serial.print(", ");
  }
  Serial.println("};");
}

  sendPacket(packet2, packet2_len);
  delay(500); // jeda antar packet

  sendPacket(packet3, packet3_len);
  delay(500);

  sendPacket(packet4, packet4_len);
  delay(500);

  sendPacket(packet5, packet5_len);
  delay(500);
  
  sendPacket(packet6, packet6_len);
  delay(500);
  
  sendPacket(packet7, packet7_len);
  delay(1000); // tunggu sebelum loop ulang


}

void printHex(int num, int precision) {
    char tmp[16];
    char format[128];
 
    sprintf(format, "%%.%dX", precision);
 
    sprintf(tmp, format, num);
    Serial.print(tmp);
}
