const fs = require('fs/promises');
const path = require('path');
const postcss = require('postcss');
const cssnano = require('cssnano');
const terser = require('terser');

const root = path.resolve(__dirname, '..');

const cssFiles = [
  'public/assets/front/css/menu.css',
  'public/assets/front/css/style.css',
  'public/assets/front/css/responsive.css',
  'public/assets/front/css/home.css',
  'public/assets/front/css/events.css',
  'public/assets/front/css/organizer.css',
];

const jsFiles = [
  'public/assets/front/js/script.js',
];

function minPath(filePath) {
  return filePath.replace(/(\.css|\.js)$/, '.min$1');
}

async function writeMinifiedCss(filePath) {
  const inputPath = path.join(root, filePath);
  const outputPath = path.join(root, minPath(filePath));
  const source = await fs.readFile(inputPath, 'utf8');
  const result = await postcss([cssnano({ preset: 'default' })]).process(source, {
    from: inputPath,
    to: outputPath,
    map: false,
  });

  await fs.writeFile(outputPath, result.css);
  return [filePath, minPath(filePath), Buffer.byteLength(source), Buffer.byteLength(result.css)];
}

async function writeMinifiedJs(filePath) {
  const inputPath = path.join(root, filePath);
  const outputPath = path.join(root, minPath(filePath));
  const source = await fs.readFile(inputPath, 'utf8');
  const result = await terser.minify(source, {
    compress: true,
    mangle: false,
    format: {
      comments: false,
    },
  });

  if (!result.code) {
    throw new Error(`No minified output generated for ${filePath}`);
  }

  await fs.writeFile(outputPath, result.code);
  return [filePath, minPath(filePath), Buffer.byteLength(source), Buffer.byteLength(result.code)];
}

async function main() {
  const results = [];

  for (const filePath of cssFiles) {
    results.push(await writeMinifiedCss(filePath));
  }

  for (const filePath of jsFiles) {
    results.push(await writeMinifiedJs(filePath));
  }

  for (const [source, output, before, after] of results) {
    const saved = before - after;
    const ratio = before > 0 ? Math.round((saved / before) * 100) : 0;
    console.log(`${source} -> ${output} (${before} -> ${after} bytes, saved ${ratio}%)`);
  }
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
