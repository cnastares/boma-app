<div class="text-sm" x-data="{
    showMainCategories: @entangle('showMainCategories'),
     id: @entangle('id'),
      parent_category: @entangle('parent_category'),
      showAgePopup: false,
       showIdentityPopup: false,
       selectedCategoryId: null,
       selectedfieldName:null,
       showChildCategory:@entangle('showChildCategory')
       }"
     >

    @if ($adSettings->admin_approval_required && ($this->ad && $this->ad->status?->value!='draft'))
    <p class="text-gray-600 mb-5 text-sm">{!! __('messages.t_title_description_admin_approval_instruction') !!}</p>
    @endif

    <!-- Age Verification Popup -->
    @include('livewire.ad.post-ad.age-verify')

    <!-- Identity Verification Popup -->
    @include('livewire.ad.post-ad.identity-verify')

    <form wire:submit>

        @if($this->canDisplayAdTypeSelect())
        <div class="mb-5">
            {{ $this->adTypeSelect() }}
        </div>
        @endif

        <div class="mb-5">
            {{ $this->titleInput() }}
        </div>
        <!-- Main Categories Section -->
        <div x-show="showMainCategories && id !== ''">
            <h3 class="mb-2">{{ __('messages.t_select_category') }}</h3>
            <x-input-error :messages="$errors->get('parent_category')" class="mb-2" />

            <div class="grid grid-cols-2 md:grid-cols-3 gap-8 mt-2">
                @foreach ($categories as $category)
                    <button type="button" wire:key="main-{{ $category->id }}" wire:click="selectMainCategory({{ $category->id }})"
                        class="ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10  text-center p-4 rounded-xl cursor-pointer bg-white classic:ring-black {{$homeSettings->enable_hover_animation?'classic-hover-shadow':''}}"
                        {{-- @click="showMainCategories = false" --}}
                        :class="parent_category === {{ $category->id }} ?
                            'bg-white border border-black border-b-4 border-r-4 ' : ''">
                    <!-- Display the category's icon or a default one. -->
                    <img src="{{ $category->icon ? asset($category->icon) : asset('/images/category-icon.svg') }}"
                        alt="{{ $category->name }}" class="mx-auto w-10 h-10">
                    <h5 class="font-bold py-3">{{ $category->name }}</h5>
                    <span class="text-sm hidden md:inline-block break-all">{{ $category->description }}</span>
                </button type="button">
                @endforeach
            </div>
        </div>


        <!-- Selected Category & Subcategories Section -->
        @if (!is_null($parent_category))
        <div class="mb-5" x-show="!showMainCategories">
            <!-- Display the chosen category notification -->
            <div class="mt-6 mb-4 font-medium">
                <div>{{ __('messages.t_you_have_chosen',['siteName' => $generalSettings->site_name]) }}
                    <strong>{{ $categories->firstWhere('id', $parent_category)?->name }}</strong>.

                    <span class="underline cursor-pointer"
                        @click="showMainCategories = true">{{ __('messages.t_change_category') }}</span>
                </div>
            </div>

            <!-- Subcategories Dropdown -->
            <div>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="sub_category_id" >
                        <option value="">{{ __('messages.t_select_subcategory') }}</option>
                        @foreach ($categories->firstWhere('id', $parent_category)?->subcategories ?? [] as $subcategory)
                        <option wire:key='category-{{ $subcategory->id }}' value="{{ $subcategory->id }}">
                            {{ $subcategory->name }}
                        </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            <x-input-error :messages="$errors->get('sub_category_id')" class="mt-2" />
        </div>

        <!-- Get Child Categories -->
        @php
            if( $parent_category && $sub_category_id){
                $subcategories = $categories->firstWhere('id', $parent_category)?->subcategories;
                $subcategory = $subcategories?->firstWhere('id', $sub_category_id); // Get the Sub Category
            }
        @endphp

<!-- Child Categories Dropdown -->
        @if (!is_null($sub_category_id) && $subcategory &&  $subcategory->subcategories->count() > 0)
        <div class="mb-5" x-show="!showMainCategories && showChildCategory">
            <div>
                {{-- <label for="child_category_id">{{ __('messages.t_select_child_category') }}</label> --}}
                <x-filament::input.wrapper>
                    <x-filament::input.select id="child_category_id" wire:model.live="child_category_id">
                        <option value="">{{ __('messages.t_select_child_category') }}</option>
                        @foreach ($subcategory->subcategories as $childCategory)
                        <option wire:key='child-category-{{ $childCategory->id }}' value="{{ $childCategory->id }}">
                            {{ $childCategory->name }}
                        </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            <x-input-error :messages="$errors->get('child_category_id')" class="mt-2" />
        </div>
        @endif
        <div class="mb-5">
            {{ $this->detailForm() }}
        </div>
        @endif

    </form>
    @if (!is_null($parent_category))

    <form wire:submit>
        @if(is_vehicle_rental_active() && app('filament')->hasPlugin('vehicle-rental-marketplace'))
        <div class="mb-5">
            {{ $this->businessSpecificForm() }}
        </div>
        @endif
    </form>
    @endif

</div>
