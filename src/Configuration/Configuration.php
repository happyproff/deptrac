<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{
    /** @var ConfigurationLayer[] */
    private $layers;
    /** @var string[] */
    private $paths;
    /** @var string[] */
    private $excludeFiles;
    /** @var ConfigurationRuleset */
    private $ruleset;
    /** @var ConfigurationSkippedViolation */
    private $skipViolations;
    /** @var bool */
    private $ignoreUncoveredInternalClasses;

    /**
     * @param array<string, mixed> $args
     *
     * @throws Exception\InvalidConfigurationException
     */
    public static function fromArray(array $args): self
    {
        $options = (new OptionsResolver())->setRequired([
            'layers',
            'paths',
            'ruleset',
        ])
        ->setDefault('exclude_files', [])
        ->setDefault('skip_violations', [])
        ->setDefault('ignore_uncovered_internal_classes', true)
        ->addAllowedTypes('layers', 'array')
        ->addAllowedTypes('paths', 'array')
        ->addAllowedTypes('exclude_files', ['array', 'null'])
        ->addAllowedTypes('ruleset', 'array')
        ->addAllowedTypes('skip_violations', 'array')
        ->addAllowedTypes('ignore_uncovered_internal_classes', 'bool')
        ->resolve($args);

        return new self($options);
    }

    /**
     * @throws Exception\InvalidConfigurationException
     */
    private function __construct(array $options)
    {
        $this->layers = array_map(static function (array $v): ConfigurationLayer {
            return ConfigurationLayer::fromArray($v);
        }, $options['layers']);

        $layerNames = array_values(array_map(static function (ConfigurationLayer $configurationLayer): string {
            return $configurationLayer->getName();
        }, $this->layers));

        $duplicateLayerNames = array_keys(array_filter(array_count_values($layerNames), static function (int $count): bool {
            return $count > 1;
        }));

        if ([] !== $duplicateLayerNames) {
            throw Exception\InvalidConfigurationException::fromDuplicateLayerNames(...$duplicateLayerNames);
        }

        $layerNamesUsedInRuleset = array_unique(array_merge(
            array_keys($options['ruleset']),
            ...array_values(array_map(static function (?array $rules): array {
                return (array) $rules;
            }, $options['ruleset']))
        ));

        $unknownLayerNames = array_diff(
            $layerNamesUsedInRuleset,
            $layerNames
        );

        if ([] !== $unknownLayerNames) {
            throw Exception\InvalidConfigurationException::fromUnknownLayerNames(...$unknownLayerNames);
        }

        $this->ruleset = ConfigurationRuleset::fromArray($options['ruleset']);
        $this->paths = $options['paths'];
        $this->skipViolations = ConfigurationSkippedViolation::fromArray($options['skip_violations']);
        $this->excludeFiles = (array) $options['exclude_files'];
        $this->ignoreUncoveredInternalClasses = (bool) $options['ignore_uncovered_internal_classes'];
    }

    /**
     * @return ConfigurationLayer[]
     */
    public function getLayers(): array
    {
        return $this->layers;
    }

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * @return string[]
     */
    public function getExcludeFiles(): array
    {
        return $this->excludeFiles;
    }

    public function getRuleset(): ConfigurationRuleset
    {
        return $this->ruleset;
    }

    public function getSkipViolations(): ConfigurationSkippedViolation
    {
        return $this->skipViolations;
    }

    public function ignoreUncoveredInternalClasses(): bool
    {
        return $this->ignoreUncoveredInternalClasses;
    }
}
