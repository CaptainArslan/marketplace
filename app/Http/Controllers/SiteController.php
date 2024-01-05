<?php

namespace App\Http\Controllers;

use App\Category;
use App\Frontend;
use App\Language;
use App\Level;
use App\Page;
use App\Product;
use App\Sell;
use App\SubCategory;
use App\Subscriber;
use App\SupportAttachment;
use App\SupportMessage;
use App\SupportTicket;
use App\User;
use App\WishlistProduct;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public $activeTemplate;
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function index($urlquery = null)
    {

        $count = Page::where('tempname', $this->activeTemplate)->where('slug', 'home')->count();

        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        if ($count == 0) {
            $page = new Page();
            $page->tempname = $this->activeTemplate;
            $page->name = 'HOME';
            $page->slug = 'home';
            $page->save();
        }
        $sections = [];
        $cats = Category::whereHas('products', function ($query) {
            $query->where('total_sell', '>=', 1);
        })->get();
        foreach ($cats as $cat) {
            $catproduct = Product::where('status', 1)->Where('total_sell', '>=', 1)
                ->where('category_id', $cat->id)
                ->whereHas('user', function ($query) {
                    $query->where('status', 1);
                })
                ->whereHas('category', function ($query) {
                    $query->where('status', 1);
                })
                ->whereHas('subcategory', function ($query) {
                    $query->where('status', 1);
                })
                ->with(['subcategory', 'user', 'category'])
                ->limit(8)
                ->latest()
                ->get();

            $sections[] = $catproduct;
        }
        $apidata = [];
        $data['page_title'] = 'Home';
        $data['categories'] = Category::where('status', 1)->with('products', 'subcategories')->get();
        $data['sections'] = Page::where('tempname', $this->activeTemplate)->where('slug', 'home')->firstOrFail();
        $pagesections = $data['sections'];
        if ($pagesections->secs != null) {
            foreach (json_decode($pagesections->secs) as $sec) {
                if ($sec == 'featured_product') {
                    $featuredProductContent = getContent('featured_product.content', true);
                    $featuredProducts = \App\Product::where('featured', 1)->where('status', 1)->whereHas('user', function ($query) {
                        $query->where('status', 1);
                    })->whereHas('category', function ($query) {
                        $query->where('status', 1);
                    })->whereHas('subcategory', function ($query) {
                        $query->where('status', 1);
                    })->with(['subcategory', 'user'])->latest()->get();
                    $apidata['featuredProductContent'] = $featuredProductContent;
                    $apidata['featuredProducts'] = $featuredProducts;
                }
                if ($sec == 'best_author_product') {
                    $bestAuthorContent = getContent('best_author_product.content', true);

                    $bestAuthorProducts = \App\Product::where('status', 1)->whereHas('user', function ($query) {
                        $query->where('status', 1);
                    })->whereHas('category', function ($query) {
                        $query->where('status', 1);
                    })->whereHas('subcategory', function ($query) {
                        $query->where('status', 1);
                    })->selectRaw('products.*, (avg_rating*total_sell) as point')
                        ->orderBy('point', 'desc')
                        ->with(['subcategory', 'user'])->limit(12)->get();
                    $apidata['bestAuthorContent'] = $bestAuthorContent;
                    $apidata['bestAuthorProducts'] = $bestAuthorProducts;
                }
            }
        }

        $data['catsections'] = $sections;
        $data['catwithmostsold'] = $cats;
        $apidata['mostsoldproducts'] = $sections;
        $apidata['catwithmostsold'] = $cats;
        $apidata['imgpath'] = asset('assets/images/product/');

        $apidata['browsecategories'] = $data['categories'];

        if (!is_null($urlquery) && $urlquery == 'homepage') {
            return response()->json($apidata);
        }


        return view($this->activeTemplate . 'home', $data);
    }

    public function pages($slug)
    {
        $page = Page::where('tempname', $this->activeTemplate)->where('slug', $slug)->firstOrFail();
        $data['page_title'] = $page->name;
        $data['sections'] = $page;
        return view($this->activeTemplate . 'pages', $data);
    }

    public function contact()
    {
        $data['page_title'] = "Contact Us";
        return view($this->activeTemplate . 'contact', $data);
    }

    public function contactSubmit(Request $request)
    {
        $ticket = new SupportTicket();
        $message = new SupportMessage();

        $imgs = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf');

        $this->validate($request, [
            'attachments' => [
                'sometimes',
                'max:4096',
                function ($attribute, $value, $fail) use ($imgs, $allowedExts) {
                    foreach ($imgs as $img) {
                        $ext = strtolower($img->getClientOriginalExtension());
                        if (($img->getSize() / 1000000) > 2) {
                            return $fail("Images MAX  2MB ALLOW!");
                        }
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg, pdf images are allowed");
                        }
                    }
                    if (count($imgs) > 5) {
                        return $fail("Maximum 5 images can be uploaded");
                    }
                },
            ],
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'subject' => 'required|max:100',
            'message' => 'required',
        ]);

        $random = getNumber();

        $ticket->user_id = auth()->id();
        $ticket->name = $request->name;
        $ticket->email = $request->email;

        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $message->supportticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $path = imagePath()['ticket']['path'];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $image) {
                try {
                    $attachment = new SupportAttachment();
                    $attachment->support_message_id = $message->id;
                    $attachment->image = uploadImage($image, $path);
                    $attachment->save();
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Could not upload your ' . $image];
                    return back()->withNotify($notify)->withInput();
                }
            }
        }
        $notify[] = ['success', 'ticket created successfully!'];

        return redirect()->route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return redirect()->back();
    }

    // public function blogDetails($id, $slug)
    // {
    //     $blog = Frontend::where('id', $id)->where('data_keys', 'blog.element')->firstOrFail();
    //     $categories = Category::where('status', 1)->latest()->get();
    //     $recentBlogs = Frontend::where('data_keys', 'blog.element')->get();
    //     $page_title = 'Blog Details';
    //     return view($this->activeTemplate . 'blogDetails', compact('blog', 'page_title', 'recentBlogs', 'categories'));
    // }

    public function placeholderImage($size = null)
    {
        if ($size != 'undefined') {
            $size = $size;
            $imgWidth = explode('x', $size)[0];
            $imgHeight = explode('x', $size)[1];
            $text = $imgWidth . '×' . $imgHeight;
        } else {
            $imgWidth = 150;
            $imgHeight = 150;
            $text = 'Undefined Size';
        }
        $fontFile = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX = ($imgWidth - $textWidth) / 2;
        $textY = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function blogs()
    {
        $page_title = 'Blogs';
        $blogElements = Frontend::where('data_keys', 'blog.element')->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'blogs', compact('page_title', 'blogElements'));
    }

    public function usernameSearch($username)
    {
        $user = User::where('status', 1)->where('username', $username)->firstOrFail();
        $page_title = $user->username;
        $totalSell = Sell::where('author_id', $user->id)->where('status', 1)->count();
        $totalProduct = Product::where('user_id', $user->id)->where('status', 1)->count();
        $levels = Level::get();
        $products = Product::where('user_id', $user->id)->where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->with(['subcategory', 'user'])->latest()->paginate(getPaginate());
        $categories1 = [];
        foreach ($products as $product) {
            $categoryForSearchPage = Category::where('id', $product->category_id)->where('status', 1)->first();
            $categories1[$categoryForSearchPage->id] = $categoryForSearchPage->name;
        }
        $categories1 = array_unique($categories1);
        $categories = Category::where('status', 1)->latest()->get();

        // $min = floor($products->min('regular_price'));
        // $max = ceil($products->max('regular_price'));

        return view($this->activeTemplate . 'author_profile', get_defined_vars());
    }

    public function productSearch(Request $request, $urlquery = null)
    {

        if (empty($request->search) && $request->search == null) {
            $notify[] = ['error ', 'Please Enter Some text'];
            return back()->withNotify($notify);
        }

        $page_title = 'Products for ' . $request->search;
        $empty_message = 'No data Found';
        $search = trim(preg_replace('/\s+/', ' ', $request->search));
        $products = Product::where('status', 1)->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%$search%")->orWhereHas('category', function ($category) use ($search) {
                $category->where('name', 'LIKE', "%$search%");
            })->orWhereHas('subcategory', function ($subcategory) use ($search) {
                $subcategory->where('name', 'LIKE', "%$search%");
            })->orWhereHas('user', function ($user) use ($search) {
                $user->where('username', $search);
            })->orwhereJsonContains('tag', $search);
        })->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->with(['category', 'user', 'subcategory'])->latest()->paginate(getPaginate());
        $wordsearch = [];
        foreach ($products as $p) {
            $wordsearch[] = $p->category->id;
        }

        $tags = $this->getTags($products->pluck('tag'));
        $categoryForSearchPage = Category::where('status', 1)->latest()->get();

        $min = floor($products->min('regular_price'));
        $max = ceil($products->max('regular_price'));
        $apidata = [];
        $apidata['allProducts'] = $products;
        $apidata['categoryForSearchPage'] = $categoryForSearchPage;
        $apidata['minPrice'] = $min;
        $apidata['maxPrice'] = $max;
        $apidata['allTags'] = $tags;
        $apidata['page_title'] = $page_title;
        if (!is_null($urlquery) && $urlquery == 'query') {
            return response()->json($apidata);
        }

        return view($this->activeTemplate . 'search', compact('page_title', 'empty_message', 'products', 'tags', 'categoryForSearchPage', 'min', 'max', 'wordsearch'));
    }

    public function categorySearch($id)
    {
        $category = Category::where('status', 1)->findOrFail($id);
        $page_title = 'Products from ' . $category->name;
        $empty_message = 'No data Found';
        $products = $category->products()->where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->with(['category', 'user', 'subcategory'])->latest()->paginate(getPaginate());

        $tags = $this->getTags($products->pluck('tag'));
        $categoryForSearchPage = Category::where('status', 1)->latest()->get();

        $min = floor($products->min('regular_price'));
        $max = ceil($products->max('regular_price'));

        return view($this->activeTemplate . 'search', compact('page_title', 'empty_message', 'products', 'tags', 'categoryForSearchPage', 'min', 'max', 'category'));
    }

    public function subcategorySearch($id)
    {

        $subcategory = SubCategory::where('status', 1)->findOrFail($id);
        $page_title = 'Products from ' . $subcategory->name;
        $empty_message = 'No data Found';
        $products = $subcategory->products()->where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->with(['category', 'user', 'subcategory'])->latest()->paginate(getPaginate());

        $tags = $this->getTags($products->pluck('tag'));

        $categoryForSearchPage = Category::where('status', 1)->latest()->get();

        $min = floor($products->min('regular_price'));
        $max = ceil($products->max('regular_price'));

        return view($this->activeTemplate . 'search', compact('page_title', 'empty_message', 'products', 'tags', 'categoryForSearchPage', 'min', 'max', 'subcategory'));
    }

    public function tagSearch($tag)
    {
        $page_title = 'Products from ' . $tag;
        $empty_message = 'No data Found';
        $products = Product::where('status', 1)->whereJsonContains('tag', $tag)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->with(['category', 'user', 'subcategory'])->latest()->paginate(getPaginate());

        $tags = $this->getTags($products->pluck('tag'));
        $categoryForSearchPage = Category::where('status', 1)->latest()->get();

        $min = floor($products->min('regular_price'));
        $max = ceil($products->max('regular_price'));

        return view($this->activeTemplate . 'search', compact('page_title', 'empty_message', 'products', 'tags', 'categoryForSearchPage', 'min', 'max', 'tag'));
    }
    public function productCategoryFilter(Request $request)
    {
        $categories = $request->categories;
        $id = $request->id;
        $query = Product::where('user_id', $id)->where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        });
        if (!is_null($categories)) {
            $query = $query->whereIn('category_id', $request->categories);
        }
        $query->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        });
        $products = $query->with(['user', 'subcategory'])->get();
        $view = view($this->activeTemplate . 'catsearchfilter', get_defined_vars())->render();
        return response()->json([
            'html' => $view,
        ]);
    }
    public function productFilter(Request $request, $urlquery = null)
    {
        // dd($request->all());
        $validate = Validator::make($request->all(), [
            'min' => 'required|numeric',
            'max' => 'required|numeric',
            'order_by' => 'nullable|in:1,2,3,4,5,6,7,8',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()]);
        }

        $categories = $request->categories ?? null;
        $tags = $request->tags ?? null;
        $search = $request->search;

        $query = Product::where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        });

        if ($search) {

            $query = $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")->orWhereHas('category', function ($category) use ($search) {
                    $category->where('name', 'LIKE', "%$search%");
                })->orWhereHas('subcategory', function ($subcategory) use ($search) {
                    $subcategory->where('name', 'LIKE', "%$search%");
                })->orWhereHas('user', function ($user) use ($search) {
                    $user->where('status', 1)->where('username', $search);
                })->orWhereJsonContains('tag', $search);
            });
        }

        if ($categories) {
            $query = $query->whereIn('category_id', $request->categories);
        }

        if ($tags) {
            $query = $query->whereJsonContains('tag', $request->tags);
        }
        if ($request->min > 0) {
            $query->where('regular_price', '>=', $request->min);
        }
        if ($request->max > 0) {
            $query->where('regular_price', '<=', $request->max);
        }

        $orderBy = "1";
        if ($request->order_by) {
            $orderBy = $request->order_by;
        }

        $query->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        });

        if ($orderBy == 1) {
            $query->orderBy('regular_price', 'ASC');
        } elseif ($orderBy == 2) {
            $query->orderBy('regular_price', 'DESC');
        } elseif ($orderBy == 3) {
            $query->orderBy('updated_at', 'ASC');
        } elseif ($orderBy == 4) {
            $query->orderBy('updated_at', 'DESC');
        } elseif ($orderBy == 5) {
            $query->orderBy('total_sell', 'ASC');
        } elseif ($orderBy == 6) {
            $query->orderBy('total_sell', 'DESC');
        } elseif ($orderBy == 7) {
            $query->orderBy('avg_rating', 'ASC');
        } elseif ($orderBy == 8) {
            $query->orderBy('avg_rating', 'DESC');
        }

        $products = $query->with(['user', 'subcategory'])->get();

        $empty_message = 'No product found';

        $view = view($this->activeTemplate . 'filtered_search', compact('products', 'empty_message', 'tags', 'categories'))->render();
        $min = floor($products->min('regular_price'));
        $max = ceil($products->max('regular_price'));
        $tags = $this->getTags($products->pluck('tag'));
        $apidata = [];
        $apidata['allProducts'] = $products;
        $apidata['categoryForSearchPage'] = $categories;
        $apidata['minPrice'] = $min;
        $apidata['maxPrice'] = $max;
        $apidata['allTags'] = $tags;
        if (!is_null($urlquery) && $urlquery == 'data') {
            return response()->json($apidata);
        }

        return response()->json([
            'html' => $view,
            'min' => $min,
            'max' => $max,
            'tags' => $tags,
            'total_item' => $products->count(),
        ]);
    }

    public function getTags($tagsArray)
    {
        $tags = [];
        foreach ($tagsArray as $value) {
            $tags = array_merge($value, $tags);
        }
        $tags = array_unique($tags);

        return $tags;
    }

    public function productDetails($slug, $id, $fetch = null)
    {
        $page_title = 'Product Details';
        $product = Product::where('status', 1)->with(['category', 'user', 'ratings', 'bumps', 'productcustomfields'])->findOrFail($id);
        $encryptedProductId = Crypt::encrypt($product->id);

        if (auth()->user()) {
            $wishlist = WishlistProduct::where('user_id', auth()->user()->id)->first();
        }
        $moreProducts = Product::where('user_id', $product->user_id)->where('status', 1)->with(['subcategory', 'user', 'ratings'])->limit(6)->inRandomOrder()->get();
        $levels = Level::get();
        $ratings = $product->ratings()->with('user')->paginate(getPaginate());
        $apidata = [];
        $apidata['product'] = $product;
        $apidata['moreProducts'] = $moreProducts;
        $apidata['levels'] = $levels;
        $apidata['ratings'] = $ratings;
        $apidata['encrypted_id'] = $encryptedProductId;
        if (!is_null($fetch) && $fetch == 'fetch') {
            return response()->json($apidata);
        }
        return view($this->activeTemplate . 'productDetails', get_defined_vars());
    }

    public function productReviews($slug, $id)
    {
        $page_title = 'Product Review';
        $product = Product::where('status', 1)->with(['category', 'user', 'ratings'])->findOrFail($id);
        $moreProducts = Product::where('user_id', $product->user_id)->where('status', 1)->with(['subcategory', 'user', 'ratings'])->limit(6)->inRandomOrder()->get();
        $levels = Level::get();
        if (auth()->user()) {
            $wishlist = WishlistProduct::where('user_id', auth()->user()->id)->first();
        }

        $ratings = $product->ratings()->with('user')->paginate(getPaginate());

        return view($this->activeTemplate . 'productReviews', get_defined_vars());
    }

    public function productComments($slug, $id)
    {
        $page_title = 'Product Comments';
        $product = Product::where('status', 1)->with(['category', 'user', 'ratings'])->findOrFail($id);
        $moreProducts = Product::where('user_id', $product->user_id)->where('status', 1)->with(['subcategory', 'user', 'ratings'])->limit(6)->inRandomOrder()->get();

        $comments = $product->comments()->with(['replies', 'user', 'replies.user'])->paginate(getPaginate());
        $levels = Level::get();

        $purchased = Sell::where('product_id', $product->id)->where('status', 1)->pluck('user_id')->toArray();

        return view($this->activeTemplate . 'productComments', compact('page_title', 'product', 'moreProducts', 'comments', 'levels', 'purchased'));
    }

    public function featured()
    {
        $page_title = 'Featured Products';
        $products = Product::where('featured', 1)->where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->with(['subcategory', 'user'])->latest()->paginate(getPaginate());

        return view($this->activeTemplate . 'products', compact('page_title', 'products'));
    }

    public function allProducts($fetch = null, Request $request)
    {
        $page_title = 'All Products';
        $empty_message = 'No data Found';
        $products = Product::where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })
        ->whereHas('category', function ($query) {
            $query->where('status', 1);
        })
        ->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })
        ->with(['subcategory', 'user'])
        ->with('user')
        // ->with('sells')
        ->latest()
        ->paginate(getPaginate());

        $tags = $this->getTags($products->pluck('tag'));
        $categoryForSearchPage = Category::where('status', 1)->latest()->get();

        $min = floor($products->min('regular_price'));
        $max = ceil($products->max('regular_price'));
        $apidata = [];
        $apidata['allProducts'] = $products;
        $apidata['categoryForSearchPage'] = $categoryForSearchPage;
        $apidata['minPrice'] = $min;
        $apidata['maxPrice'] = $max;
        $apidata['allTags'] = $tags;

        if ($request->is('api/*')) {
            return $this->respondWithSuccess($apidata, 'All Products');
        }

        return view($this->activeTemplate . 'search', compact('page_title', 'empty_message', 'products', 'tags', 'categoryForSearchPage', 'min', 'max'));
    }
    public function reset($id)
    {
        $products = Product::where('user_id', $id)->where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->with(['subcategory', 'user'])->latest()->paginate(getPaginate());
        return response()->json($products);
    }
    public function resetfilter()
    {
        $page_title = 'All Products';
        $empty_message = 'No data Found';
        $products = Product::where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->with(['subcategory', 'user'])->latest()->paginate(getPaginate());

        $tags = $this->getTags($products->pluck('tag'));
        $categoryForSearchPage = Category::where('status', 1)->latest()->get();

        $min = floor($products->min('regular_price'));
        $max = ceil($products->max('regular_price'));

        $html = view($this->activeTemplate . 'filtered_search', get_defined_vars())->render();
        return response()->json([
            'html' => $html,
            'tags' => $tags,
            'products' => $products,
        ]);
    }
    public function bestSell()
    {
        $page_title = 'Best Sold Products';
        $products = Product::where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->orderBy('total_sell', 'desc')->with(['subcategory', 'user'])->latest()->paginate(getPaginate());

        return view($this->activeTemplate . 'products', compact('page_title', 'products'));
    }
    public function bestcategory($id)
    {

        $products = Product::where('status', 1)->where('category_id', $id)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->orderBy('total_sell', 'desc')->with(['subcategory', 'user'])->latest()->paginate(getPaginate());
        foreach ($products as $prod) {
            $page_title = 'Best Sold Product of ' . $prod->category->name;
        }

        return view($this->activeTemplate . 'products', compact('page_title', 'products'));
    }

    public function bestAuthor()
    {
        $page_title = 'Best Author Products';
        $products = Product::where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->orderBy('avg_rating', 'desc')->with(['subcategory', 'user'])->paginate(getPaginate());

        return view($this->activeTemplate . 'products', compact('page_title', 'products'));
    }

    public function authorProducts($username)
    {
        $user = User::where('status', 1)->where('username', $username)->firstOrFail();
        $page_title = $user->username . '- Products';

        $products = Product::where('status', 1)->where('user_id', $user->id)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereHas('subcategory', function ($query) {
            $query->where('status', 1);
        })->with(['subcategory', 'user'])->paginate(getPaginate());

        return view($this->activeTemplate . 'products', compact('page_title', 'products'));
    }

    public function subscriberStore(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()]);
        }

        $subscriber = new Subscriber();
        $subscriber->email = $request->email;
        $subscriber->save();

        return response()->json(['success' => 'Subscribed Successfully!']);
    }

    public function policy($id, $heading)
    {
        $policy = Frontend::where('data_keys', 'policy.element')->findOrFail($id);
        $page_title = $heading;

        return view($this->activeTemplate . 'policy', compact('page_title', 'policy'));
    }

    public function suppotDetails()
    {
        $policy = Frontend::where('data_keys', 'support.content')->first();
        $page_title = 'Support Details';

        return view($this->activeTemplate . 'policy', compact('page_title', 'policy'));
    }

    public function cookieAccept()
    {
        session()->put('cookie_accepted', true);
        return response()->json(['success' => 'Cockie accepted successfully']);
    }
}
