<?php

declare(strict_types=1);

enum MoodState: string {
    case NORMAL = 'normal';
    case TANTRUM = 'tantrum';
}

class AppearanceProfile {
    public function __construct(
        public readonly string $description,
        public readonly string $textColor,
        public readonly string $backgroundColor
    ) {}
}

class CharacterVisualizer {
    public function getProfile(MoodState $state): AppearanceProfile {
        return match ($state) {
            MoodState::NORMAL => new AppearanceProfile(
                'beautiful',
                '#FFFFFF',
                '#000000'
            ),
            MoodState::TANTRUM => new AppearanceProfile(
                'black',
                '#000000',
                '#FFFFFF'
            )
           
        };
    }
}

$visualizer = new CharacterVisualizer();
$currentMood = MoodState::TANTRUM;
$profile = $visualizer->getProfile($currentMood);

echo sprintf(
    "<div style='color: %s; background-color: %s; padding: 10px; font-family: monospace; font-weight: bold; text-align: center;'>
        STATE: %s | VISUAL: %s
    </div>",
    $profile->textColor,
    $profile->backgroundColor,
    strtoupper($currentMood->value),
    strtoupper($profile->description)
);
