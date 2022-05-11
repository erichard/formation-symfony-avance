<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DispatchRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Khill\Duration\Duration;

#[ORM\Entity(repositoryClass: DispatchRequestRepository::class)]
class DispatchRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'json', options: ['jsonb' => true])]
    private $items = [];

    #[ORM\Column(type: 'string', nullable: true)]
    private $source;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'json', nullable: true)]
    private $decisionLog = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private $result = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private $stocks = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private $PTResult;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $PTDuration;

    #[ORM\Column(type: 'decimal', precision: 4, scale: 3)]
    private $duration;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $valid;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $cartId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $cartUrl;

    public function __clone()
    {
        if (!$this->id) {
            return;
        }

        $this->id = null;
        $this->createdAt = new \DateTimeImmutable();
        $this->result = [];
        $this->stocks = [];
        $this->duration = [];
        $this->decisionLog = [];
        $this->valid = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDecisionLog(): ?array
    {
        return $this->decisionLog;
    }

    public function setDecisionLog(?array $decisionLog): self
    {
        $this->decisionLog = $decisionLog;

        return $this;
    }

    public function markDecisionFailed(string $message): self
    {
        $this->decisionLog[] = [
            'type' => 'failed',
            'message' => $message,
        ];

        return $this;
    }

    public function markDecisionPassed(string $message): self
    {
        $this->decisionLog[] = [
            'type' => 'success',
            'message' => $message,
        ];

        return $this;
    }

    public function getResult(): ?array
    {
        return $this->result;
    }

    public function checkConformity()
    {
        if (null === $this->PTResult) {
            $this->valid = null;

            return;
        }

        $result = $this->result;
        ksort($result);

        $ptResult = $this->PTResult;

        if (is_array($ptResult)) {
            ksort($ptResult);
            $this->valid = json_encode($ptResult) === json_encode($result);
        } elseif (false === $ptResult) {
            $this->valid = empty($result);
        }

        return $this->valid;
    }

    public function setResult(?array $result): self
    {
        $this->result = $result;

        $this->checkConformity();

        return $this;
    }

    public function getStocks(): ?array
    {
        return $this->stocks;
    }

    public function setStocks(?array $stocks): self
    {
        $this->stocks = $stocks;

        return $this;
    }

    public function getPTResult(): bool|array|null
    {
        return $this->PTResult;
    }

    public function setPTResult(bool|array|null $PTResult): self
    {
        $this->PTResult = $PTResult;

        return $this;
    }

    public function getPTDuration(): ?int
    {
        return $this->PTDuration;
    }

    public function setPTDuration(?int $PTDuration): self
    {
        $this->PTDuration = $PTDuration < 100 ? $PTDuration * 1000 : $PTDuration;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string|float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function getFormattedTimings(): string
    {
        $omsDuration = (new Duration($this->duration))->humanize();

        $timings = $omsDuration;

        if ($this->PTDuration > 0) {
            $ptDuration = null === $this->PTDuration ? '-' : (new Duration($this->PTDuration / 1000))->humanize();
            $ratio = round((1 - ($this->duration * 1000) / $this->PTDuration) * 100);
            $timings = $omsDuration. ' <small class="text-muted"> -> '.$ptDuration. ' ( -'.$ratio.'%)</small>';
        }

        return $timings;
    }

    public function getCartId(): ?string
    {
        return $this->cartId;
    }

    public function setCartId(?string $cartId): self
    {
        $this->cartId = $cartId;

        return $this;
    }

    public function getCartUrl(): ?string
    {
        return $this->cartUrl;
    }

    public function setCartUrl(?string $cartUrl): self
    {
        $this->cartUrl = $cartUrl;

        return $this;
    }

    public function isDispatchedFromOrliweb(): bool
    {
        foreach ($this->result as $ean => $dispatch) {
            foreach ($dispatch as $warehouse => $quantity) {
                if (false !== mb_strpos($warehouse, 'ORLI-')) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getItemsEANs(): array
    {
        return array_column($this->items, 'ean');
    }

    public function getQuantity(): int
    {
        $quantity = 0;

        foreach ($this->items as $item) {
            $quantity += $item['quantity'];
        }

        return $quantity;
    }
}
