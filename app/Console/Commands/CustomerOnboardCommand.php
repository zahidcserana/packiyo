<?php

namespace App\Console\Commands;

use App\Http\Requests\Customer\StoreRequest as CustomerStoreRequest;
use App\Http\Requests\User\StoreRequest as UserStoreRequest;
use App\Models\Customer;
use App\Models\CustomerUserRole;
use App\Models\TaskType;
use App\Models\UserRole;
use Illuminate\Console\Command;
use Webpatser\Countries\Countries;

class CustomerOnboardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:onboard
                            {--i|interactive : Run in interactive mode (other arguments are then ignored)}
                            {--c|customer= : Customer name}
                            {--C|country= : 2 letter country code}
                            {--u|user= : Name of the first user}
                            {--e|email= : Email address of the first user}
                            {--p|password= : Password for the first user}
                            {--3pl= : 3PL Customer ID}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new customer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->option('interactive')) {
            $customerName = $this->option('customer');
            $countryCode = $this->option('country');
            $userName = $this->option('user');
            $email = $this->option('email');
            $password = $this->option('password');
            $parentCustomerId = $this->option('3pl');
        } else {
            $countries = Countries::pluck('name', 'iso_3166_2')->toArray();
            $parentCustomers = Customer::with('contactInformation')->whereNull('parent_id')->get()->map(fn($customer) => $customer->id . ': ' . $customer->contactInformation->name)->toArray();

            array_unshift($parentCustomers, __('None'));

            $customerName = $this->ask('Customer name');
            $countryCode = $this->choice('Country', $countries);
            $userName = $this->ask('Name of the first user');
            $email = $this->ask('Email address of the first user');
            $password = $this->secret('Password for the first user');
            $parentCustomerId = $this->choice('3PL Customer', $parentCustomers);

            $parentCustomerId = intval(explode(':', $parentCustomerId)[0]);
        }

        $country = Countries::where('iso_3166_2', $countryCode)->first();

        if (!$customerName || !$userName || !$email || !$password || !$country) {
            $this->error('Please supply customer name, country code, user name, email and password');
            exit;
        }

        $customer = $this->createCustomer($customerName, $country, $parentCustomerId);
        $user = $this->createUser($customer, $country, $userName, $email, $password);

        $this->info('User access token:');
        $this->newLine();
        $this->info($user->createToken('onboard')->plainTextToken);
    }

    private function createCustomer($customerName, Countries $country, $parentCustomerId)
    {
        $customer = app('customer')->store(CustomerStoreRequest::make([
            'contact_information' => [
                'name' => $customerName,
                'country_id' => $country->id
            ]
        ]));

        if ($parentCustomerId) {
            $customer->update([
                'parent_id' => $parentCustomerId
            ]);
        } else {
            TaskType::create([
                'customer_id' => $customer->id,
                'type' => TaskType::TYPE_PICKING
            ]);
        }

        return $customer;
    }

    private function createUser(Customer $customer, Countries $country, $userName, $email, $password)
    {
        $user = app('user')->store(UserStoreRequest::make([
            'contact_information' => [
                'name' => $userName,
                'country_id' => $country->id
            ],
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'customer_id' => $customer->id,
            'user_role_id' => UserRole::ROLE_MEMBER,
            'customer_user_role_id' => CustomerUserRole::ROLE_CUSTOMER_ADMINISTRATOR
        ]));

        return $user;
    }
}
