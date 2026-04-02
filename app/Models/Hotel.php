<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Hotel extends Model
{
    use HasFactory;

    protected static ?array $tableColumns = null;

    protected $fillable = [
        'name',
        'hotel_name',
        'slug',
        'logo',
        'email',
        'phone',
        'address',
        'status',
    ];

    protected static function hasTableColumn(string $column): bool
    {
        if (static::$tableColumns === null) {
            static::$tableColumns = Schema::getColumnListing((new static())->getTable());
        }

        return in_array($column, static::$tableColumns, true);
    }

    public static function findByTenantSlugOrFail(string $value): self
    {
        $normalizedValue = Str::lower(trim($value));

        return static::query()
            ->where(function ($query) use ($value, $normalizedValue) {
                if (is_numeric($value)) {
                    $query->orWhereKey((int) $value);
                }

                if (static::hasTableColumn('slug')) {
                    $query->orWhere('slug', $value)
                        ->orWhere('slug', $normalizedValue);
                }

                if (static::hasTableColumn('hotel_name')) {
                    $query->orWhere('hotel_name', $value)
                        ->orWhereRaw("LOWER(REPLACE(hotel_name, ' ', '-')) = ?", [$normalizedValue]);
                }

                if (static::hasTableColumn('name')) {
                    $query->orWhere('name', $value)
                        ->orWhereRaw("LOWER(REPLACE(name, ' ', '-')) = ?", [$normalizedValue]);
                }
            })
            ->firstOrFail();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field !== null) {
            return parent::resolveRouteBinding($value, $field);
        }

        return static::findByTenantSlugOrFail((string) $value);
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['name'] ?? $this->attributes['hotel_name'] ?? null;
    }

    public function setNameAttribute(string $value): void
    {
        if (static::hasTableColumn('hotel_name')) {
            $this->attributes['hotel_name'] = $value;
            return;
        }

        $this->attributes['name'] = $value;
    }

    public function getSlugAttribute(): string
    {
        if (static::hasTableColumn('slug') && !empty($this->attributes['slug'])) {
            return $this->attributes['slug'];
        }

        return Str::slug($this->attributes['hotel_name'] ?? $this->attributes['name'] ?? 'hotel');
    }

    public function getLogoAttribute(): ?string
    {
        return $this->attributes['logo'] ?? null;
    }

    /**
     * Relationships
     */

    // Hotel has many users (managers/staff)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Hotel has many room types
    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    // Hotel has many rooms
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // Hotel has many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(RoomHousekeeping::class);
    }
}
