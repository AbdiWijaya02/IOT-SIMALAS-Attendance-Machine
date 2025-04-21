#include <ModbusRTU.h>

#define RX2 16       // GPIO16 sebagai RX2
#define TX2 17       // (tidak digunakan, cukup RX)
#define DE_RE_PIN 4  // Pin kontrol DE/RE (set ke LOW untuk mode terima)

const int MAX_PAKET = 256;

void setup() {
  pinMode(DE_RE_PIN, OUTPUT);
  digitalWrite(DE_RE_PIN, LOW);  // Mode RX

  Serial.begin(115200);
  Serial2.begin(115200, SERIAL_8N1, RX2, TX2);

  Serial.println("ESP32 - Siap menerima data RS485...");
}

void loop() {
  static uint8_t buffer[MAX_PAKET];
  static int index = 0;

  while (Serial2.available()) {
    uint8_t byteData = Serial2.read();
    if (index < MAX_PAKET) {
      buffer[index++] = byteData;
    }
    delayMicroseconds(500);
  }

  if (index > 0) {
    Serial.println("Data diterima:");

    for (int i = 0; i < index; i++) {
      Serial.print("0x");
      if (buffer[i] < 0x10) Serial.print("0");
      Serial.print(buffer[i], HEX);
      if (i < index - 1) Serial.print(", ");
      if ((i + 1) % 16 == 0 && i < index - 1) Serial.print("\n  ");
    }

    Serial.println("\n};\n");

    index = 0;
  }
}