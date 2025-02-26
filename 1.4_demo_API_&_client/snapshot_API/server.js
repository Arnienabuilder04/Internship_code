const express = require("express");
const puppeteer = require("puppeteer");
const fs = require("fs");
const path = require("path");

const app = express();
const PORT = 4000;

// Middleware
app.use(express.json());

// Ensure the screenshots directory exists
const screenshotDir = path.join(__dirname, "screenshots");
if (!fs.existsSync(screenshotDir)) {
    fs.mkdirSync(screenshotDir, { recursive: true });
}

// API Route to Capture Screenshot
app.get("/screenshot", async (req, res) => {
    const { url } = req.query;

    if (!url) {
        return res.status(400).json({ error: "Missing 'url' parameter" });
    }

    try {
        const browser = await puppeteer.launch();
        const page = await browser.newPage();
        
        await page.setViewport({ width: 1280, height: 720 });
        await page.goto(url, { waitUntil: "networkidle2" });

        // Generate file name
        const domain = new URL(url).hostname.replace(/\./g, "_");
        const filename = `${domain}.png`;
        const filepath = path.join(screenshotDir, filename);

        await page.screenshot({ path: filepath });

        await browser.close();

        res.json({
            name: filename,
            screenshot_url: `http://localhost:${PORT}/screenshots/${filename}`
        });

    } catch (error) {
        res.status(500).json({ error: "Failed to capture screenshot", details: error.message });
    }
});

app.use("/screenshots", express.static(screenshotDir));

app.listen(PORT, () => {
    console.log(`Screenshot API is running on http://localhost:${PORT}`);
});
