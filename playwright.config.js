const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/playwright',
  timeout: 90_000,
  fullyParallel: true,
  retries: 0,
  reporter: [
    ['list'],
    ['html', { open: 'never', outputFolder: 'playwright-report' }],
  ],
  use: {
    // Los assets del frontend se generan contra el host del request de cada
    // entorno (127.0.0.1 en el stack docker local/CI) → baseURL debe coincidir
    // para que img-src 'self' no viole CSP (FF-001).
    baseURL: 'http://127.0.0.1:8801',
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
    // Tolerancia estricta por defecto; los elementos dinámicos (slideshow del
    // hero del home) se enmascaran por-test en visual.spec.js.
    toHaveScreenshot: { maxDiffPixels: 200 },
  },
});
