
@extends('layouts.template')


@section('title')
    Welcome
@endsection

<div class="modal fade" id='post_modal' tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
</div>

@section('content')
    <div class="col-md-6 gedf-main">
        <!--- \\\\\\\Post-->
        <div class="card gedf-card">
            <div class="card-body">
                <div class="card-deck" id="cardDeck">
                    @if($firstStory->count() > 0)
                    @foreach($firstStory as $fStory)
                    <div class="card">
                        @if($fStory->firstStory->user_id == Auth()->user()->id)
                        <button clas="del" style="position: absolute;border: none;background: none;" onclick="deleteStory('{{ route('storyDelete', ['id' => $fStory->firstStory->id])}} ')">
                            <i style="color:red;" class="fas fa-trash"></i>
                        </button>
                        @endif
                        <img  data-toggle="modal" data-target="#bd-example-modal-xl{{ $fStory->id }}"  class="card-img-top" src="{{ asset('images/story/'.$fStory->firstStory->image) }}" alt="Card image cap">
                        <div class="card-footer">
                            <small class="text-muted">{{ $fStory->name }}</small>
                        </div>
                    </div>
                        <!-- Modal for story -->
                        <div class="modal fade" id="bd-example-modal-xl{{ $fStory->id }}" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="card-body">
                                        <div class="card-deck">
                                            <div class="card">
                                                <img src="{{ asset('images/story/'.$fStory->firstStory->image) }}" class="card-img-top" alt="...">
                                                <div class="card-body">
                                                    <p class="card-text"><small class="text-muted">Posted at: {{ $fStory->firstStory->created_at }}</small></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End modal for story -->
                    @endforeach
                    @else
                    <h1 style="width: 100%;text-align: center;">No story</h1>
                    @endif
                </div>
            </div>
        </div>
        <!-- Post /////-->

        <!--- \\\\\\\Post-->
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif
        <div class="card gedf-card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="posts-tab" data-toggle="tab" href="#posts" role="tab" aria-controls="posts" aria-selected="true">Post</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="posts-tab" data-toggle="tab" href="#story" role="tab" aria-controls="posts" aria-selected="true">Story</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="posts" role="tabpanel" aria-labelledby="posts-tab">
                        <div class="form-group">
                            <label class="sr-only" for="message">post</label>
                            <textarea class="form-control" id="message" rows="3" placeholder="What are you thinking?"></textarea>
                        </div>
                        <div id="message_status" class="h5">

                        </div>
                        <div class="custom-file" style="margin-bottom:1rem;">
                            <input type="file" name="picture" class="custom-file-input" id="picture" multiple accept="image/x-png, image/gif, image/jpeg, image/jpg">
                            <label class="custom-file-label" for="customFile">Upload image</label>
                        </div>
                        <div class="btn-toolbar justify-content-between">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary" onclick="storePost('{{ route('post.store') }}')")>share</button>
                                @csrf
                            </div>
                            <div class="btn-group">
                                <select id="is-public" class="form-control form-control-sm">
                                    <option value=1 selected>Public</option>
                                    <option value=0>Private</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="story" role="tabpanel" aria-labelledby="posts-tab">



                            @csrf
                            <div class="custom-file" style="margin-bottom:1rem;">
                                <input type="file" class="custom-file-input" id="storyImage" name="storyImage">
                                <label class="custom-file-label" for="customFile">Upload image</label>
                            </div>
                            <div class="btn-toolbar justify-content-between">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary"  onclick="storeStory('{{ route('story.store') }}')">share</button>
                                </div>

                            </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Post /////-->

        <div id="post_wrap">
        <!--- \\\\\\\Post-->
            @include('posts/partial_render')
        </div>
    </div>
    <div class="col-md-3">
        <div class="card gedf-card">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <div class="h6 text-muted">Notification</div>
                    <li class="list-group-item">
                        You have {{ $countRequests }} friend request
                        You have {{ $countMessages }} message
                    </li>
                    @foreach($users as $user)
                        <li class="list-group-item"><a href=""><img class="rounded-circle" width="45" src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png" alt=""> <span>{{ $user->name }}</span> </a>
                        <form style="float: right;padding: 8px;" action="{{ route('request',['id'=>$user->id]) }}" method="POST">
                            @csrf
                            <button style="border: none;background: #007bff;color: white;">@if($user->ifFriendRequestAccept || $user->ifAuthRequestAccept) Friend @elseif($user->ifSendRequest) Unsend request @elseif($user->ifReceiveRequest) Accept friend @else Send request @endif</button>
                        </form>
                        </li>
                    @endforeach
                </ul>
                </ul>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @parent
    <script src="{{ asset('js/story.js') }}"></script>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/posts.js') }}"></script>
@endsection
