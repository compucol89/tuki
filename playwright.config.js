const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/playwright',
  timeout: 90_000,
  fullyParallel: true,
  retries: 0,
  reporter: [['list']],
  use: {
    baseURL: 'http://localhost:8801',
    viewport: { width: 1440, height: 900 },
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
  expect: {
    // Tolerancia documentada: banda y≈800-900 del home contiene un elemento
    // dinámico del hero (~1.2k px, verificado GATE 6); el resto de la página
    // es estable pixel a pixel.
    toHaveScreenshot: { maxDiffPixels: 2000 },
  },
});
