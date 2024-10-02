import { createRequire } from "module";
const require = createRequire(import.meta.url);
const fs = require('fs')

function generateRandomString(length) {
    const characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        result += characters[randomIndex];
    }
    return result;
}

// function generateMockupURLs(baseURL, count) {
//     const urls = [];
//     for (let i = 0; i < count; i++) {
//         const randomString = generateRandomString(8);
//         urls.push(`${baseURL}/${randomString}`);
//     }
//     return urls;
// }

// Usage

const urls = [];
    for (let i = 0; i < 10000; i++) {
        const randomString = generateRandomString(8);
        urls.push('https://trackme.info/t/'+randomString);
    }

fs.writeFile('mockup.txt', urls.join('\n'), err => {
  if (err) {
    console.error(err);
  } else {
    // file written successfully
  }
});

console.log(urls);