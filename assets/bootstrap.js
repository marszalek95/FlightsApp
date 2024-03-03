// assets/bootstrap.js
import { startStimulusApp } from '@symfony/stimulus-bridge';

console.log('Initializing Stimulus app...');
// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

console.log('Stimulus app initialized.');