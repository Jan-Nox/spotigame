<h1>Create Sitting</h1>

<div class="mb-3">
    <label for="exampleFormControlInput1" class="form-label">Playmode</label>

    <select class="form-select" aria-label="Default select example" id="exampleFormControlInput1">
        <option selected>Random Songs</option>
        <option value="1">Playlist</option>
        <option value="2">Genre</option>
        <option value="3">Decades</option>
        <option value="4">Genre &amp; Decades</option>
    </select>
</div>

<div class="mb-3">
    <label for="level2" class="form-label">Secondary PLaymode</label>

    <select class="form-select" aria-label="Default select example" id="level2">
        <option selected>Random Songs</option>
        <option value="1">Playlist</option>
        <option value="2">Genre</option>
        <option value="3">Decades</option>
        <option value="4">Genre &amp; Decades</option>
    </select>
</div>

<div class="mb-3">
    <label for="level3" class="form-label">Number of Songs</label>
    <br/>
    <div class="input-group">

        <span class="input-group-text rangeMover" data-offset="-10" id="basic-addon1">-10</span>
        <span class="input-group-text rangeMover" data-offset="-1" id="basic-addon1">-1</span>
        <input type="number" min="5" max="50" value="10" class="year form-control rangeDisplay text-center">
        <span class="input-group-text rangeMover" data-offset="1" id="basic-addon2">+1</span>
        <span class="input-group-text rangeMover" data-offset="10" id="basic-addon2">+10</span>
        <input type="range" name="year" class="year form-range" value="10" min="5" max="50" id="customRange2">
    </div>
</div>

<div class="mb-3">
    <label for="level3" class="form-label">Select Questions</label>
    <br/>
    <div class="input-group mb-3">
        <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
        </div>
        <span class="input-group-text">Artist</span>
        <input type="number" value="1" class="form-control" aria-label="Text input with checkbox">
    </div>
    <div class="input-group mb-3">
        <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
        </div>
        <span class="input-group-text">Album</span>
        <input type="number" value="1" class="form-control" aria-label="Text input with checkbox">
    </div>
    <div class="input-group mb-3">
        <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
        </div>
        <span class="input-group-text">Song</span>
        <input type="number" value="1" class="form-control" aria-label="Text input with checkbox">
    </div>
    <div class="input-group mb-3">
        <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
        </div>
        <span class="input-group-text">Release</span>
        <input type="number" value="1" class="form-control" aria-label="Text input with checkbox">
    </div>
</div>


<div class="row">
    <div class="col-md-6 text-center">
        <button class="btn btn-lg btn-primary">🎮 Start single player</button>
    </div>
    <div class="col-md-6 text-center">
        <button class="btn btn-lg btn-primary">👥 Start multiplayer</button>
    </div>
</div>
