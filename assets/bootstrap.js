// assets/bootstrap.js
import '@symfony/stimulus-bundle'; // Charge le bundle Stimulus.

import { startStimulusApp } from '@symfony/stimulus-bundle';

// Initialiser Stimulus
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));