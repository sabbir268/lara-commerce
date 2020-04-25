<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail,JWTSubject
{
  use Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  public function wishlists()
  {
    return $this->hasMany(Wishlist::class);
  }

  public function customer()
  {
    return $this->hasOne(Customer::class);
  }

  public function seller()
  {
    return $this->hasOne(Seller::class);
  }

  public function products()
  {
    return $this->hasMany(Product::class);
  }

  public function shop()
  {
    return $this->hasOne(Shop::class);
  }

  public function staff()
  {
    return $this->hasOne(Staff::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }

    public function wallets()
    {
        return $this->hasMany(Wallet::class)->orderBy('created_at', 'desc');
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    
}
