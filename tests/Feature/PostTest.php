<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;

class PostTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

    }

    private function authoriseUser($action){
        Gate::define($action, function (User $user, $model) {
            return $user->id == $model->user_id;
        });
    }

    /**
     * @param Integer $number
     * @return \Illuminate\Testing\TestResponse
     */
    private function create_posts($number){

        for($iteration = 0; $iteration < $number; $iteration++) {

            if($iteration % 2 === 0) {

                $this->post('/post', [
                    'message' => $this->faker()->text(100),
                    'public' => ($iteration !== 0),
                    'picture' => UploadedFile::fake()->image(uniqid() . '.jpg')
                ]);
            } else {

                 $this->post('/post', [
                    'public' => true,
                    'message' => $this->faker()->text(100)
                ]);
            }
        }
    }

    /**
     * tests if various posts are being inserted
     */
    public function testPostInsertion(){
        $this->actingAs(User::factory()->create());
        $this->create_posts(20);
        $this->assertCount(20, Post::all(),'not all tests posts have been created');
    }

    /**
     * Tests if different user cant see private posts of the first user
     */
    public function testUserCantSeePrivatePosts(){
        $this->testPostInsertion();
        $this->actingAs(User::factory()->create());

        $postService = new PostService();
        $this->assertCount(19, $postService->getAllPosts(),'not all tests posts have been created');
    }

    public function testUserCanDeletePosts(){
        $this->actingAs(User::factory()->create());

        $this->create_posts(1);
        $post = Post::where(['user_id' => auth()->user()->id])->first();

        $this->authoriseUser('delete_post', $post, auth()->user());
        $response = $this->delete('post/' . $post->id);
        $response->assertStatus(200);
        $this->assertCount(0, Post::all(), 'Model is still in the database');

    }

    public function testUnathorisedUserCantDeletePost(){
        $this->actingAs(User::factory()->create());
        $this->create_posts(1);
        $post = Post::all()->first();

        $this->actingAs(User::factory()->create());
        $this->authoriseUser('delete_post');

        $response = $this->delete('post/' . $post->id);
        $response->assertStatus(403);
        $this->assertCount(1, Post::all(), 'Unauthorised user deleted the post');

    }

    /**
     * Deletes all saves images
     */
    protected function tearDown(): void
    {
        foreach(Post::all() as $post){
            if(isset($post->pictures->file)) {
                Storage::disk('images')->delete($post->pictures->file);
            }
        }
        parent::tearDown(); // TODO: Change the autogenerated stub
    }
}
