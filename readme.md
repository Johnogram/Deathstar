# Deathstar
Guiding the Alliance X-wings to the vulnerable area!

## Getting started
Project written in Laravel, so the requirements are the same
https://laravel.com/docs/5.8/installation#server-requirements

### Running the project
```
git clone git@github.com:Johnogram/Deathstar.git deathstar-john-minns
composer install
npm install (or yarn install)
npm run production (or yarn run production)
php artisan serve
```

### Process and Insight
The majority of my code for this project is contained in the following files:
```
app/Droids/Droid.php
app/Traits/FlightControl.php
```

All of the completed code is in master.
The development branch is available to see my commit history.
I've also left in development-BAD which contains my first, failed attempt at the solution

#### First attempt
My first attempt, whilst laying the foundation for the complete solution, fell short.
This was based around the idea of flying forward until crashing, then doing the following:
```
Take one step left, check if we can go forward
Take one step right, check if we can go forward
Take two steps left, check if we can go forward
Take two steps right, check if we can go forward
etc...
```
Also, we were only moving forward one step, making an API call, checking the response then proceeding accordingly.

Which ever returned true first would be used, then we would fly foward until another crash, rinse and repeat.

Whilst this solution would have worked, it wasn't very elegant, felt a little like brute force and had some serious
performance issues.

Whilst trying to debug my way around a maximum function recurssion limit, (making lord knows how many API polls),
I realised the solution was in the API response (something I had foolishly disregarded until this point :/ )

#### The solution
Deleting out quite a few lines of code from `Droid.php` I started on my 2nd attempt.

This time batching forward movements into groups of 512, enough that we should always meet either the finish or a wall
whilst still being a reasonable payload to deliver to the API.

When a crash happened, I took 2 pieces of information from the response; the crash co-ordinates
and the last part of the map (which showed available gaps).

Taking these 2 bits of information, performing some array and string manipulation I was able to figure out the nearest open gap to navigate to.

This meant that from flying forward, to crashing, to flying forward again, only 2 API calls were needed each time,
where as solution 1, that number could have been infinite (and an infinite loop!)

Once the destination was reach, I retrieved the map to return in the front-end view but also to check the length
to confirm the final path. Since all forward movements we're done in 512 blocks,  if the final few parts of the path had been
```
ffllfff(finish)
```
my path would always end far beyond this point
```
ffll(f*512)
```
Using the size of the map (converted to array and length of array retrieved) I could truncate the path back down
to it's correct length

### Possible Improvements
#### Performance
If this were a commercial project, I would likely move the business logic of the requests to the API and subsequent
parsing of the response into an AJAX handler or it's own micro API, whilst having a seperate front-end.

This would prevent the loading time between clicking go and the time to first byte of the results, providing a better user experience.

#### Shortest route
The route taken is pretty short, for the destination which is 501 rows from the start, my droid took 583 steps to reach the end.

In theory, this could be shorter on the basis of "The shortest distance between 2 points is a straight line" and
the Triangle Inequality Theorem (I just wikipedia'd that) where the length of the longest side of a triangle
is always shorter than the sum of the other 2 sides, if a path forward and then left/right more than 1 step could
be expressed as a triangle, then the diagonal route, would in theory, be shorter.

However as the droid can't move diagonally, instead it would have to `flfl`, this may not be possible in
the constraints of this project.
