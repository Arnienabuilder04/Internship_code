const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

(async () => {
    // Get the URL and name from command line arguments
    const url = process.argv[2]; 
    const name = process.argv[3];

    if (!url || !name) {
        console.error("Error: URL or Name not provided!");
        process.exit(1);
    }

    // Ensure the screenshots directory exists
    const screenshotDir = path.join(__dirname, "../assets/screenshots");
    if (!fs.existsSync(screenshotDir)) {
        fs.mkdirSync(screenshotDir, { recursive: true });
    }

    // Define the screenshot filename
    const filename = path.join(screenshotDir, `${name}.png`);

    try {
        const browser = await puppeteer.launch();
        const page = await browser.newPage();

        // Set viewport
        await page.setViewport({ width: 1280, height: 720 });

        // Open URL in the browser
        await page.goto(url, { waitUntil: 'networkidle2' });

        // Capture screenshot
        await page.screenshot({ path: filename });

        console.log(`Screenshot saved: ${filename}`);

        await browser.close();
    } catch (error) {
        console.error("Screenshot capture failed:", error);
    }
})();
