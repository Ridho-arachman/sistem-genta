const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

const input = 'D:/Coding/sistem-genta/cropped-cropped-SEKNAS-GENTA-PANGAN.webp';
const outDir = 'D:/Coding/sistem-genta/public/icons';

if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

const sizes = [48, 72, 96, 128, 144, 152, 192, 256, 384, 512];

async function generate() {
  for (const size of sizes) {
    const out = path.join(outDir, 'icon-' + size + '.png');
    await sharp(input).resize(size, size).png().toFile(out);
    console.log('Generated: icon-' + size + '.png');
  }
  
  for (const size of [16, 32, 48]) {
    const out = 'D:/Coding/sistem-genta/public/favicon-' + size + '.png';
    await sharp(input).resize(size, size).png().toFile(out);
    console.log('Generated: favicon-' + size + '.png');
  }
  
  console.log('Done. Use ImageMagick to combine favicon-16/32/48.png -> favicon.ico');
}

generate().catch(console.error);
