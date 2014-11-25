solin-twiner
============

Solin Twiner is ifttt.com for Moodle. It is a local plugin which lets you define an action to be performed in the case of a specific event. First, you select the event you want to respond to. Then you compose the actual response, the action, out of a few specific building blocks.

As an example, let’s say you want receive an email whenever a new user registers. First, you select the event 'User created' from the list of all Moodle events. Then you choose the type of action you want to use: 'Notification'. This action type lets you specify a subject, message and destination (the Moodle user to be notified) - usually yourself.

Now, whenever a new user registers (or is otherwise created, of course), you automatically receive a Moodle notification about it!

Another example is: if a new user is created, enroll this user into a specific course or set of courses.



// Fixme - add readme for
// classes with static function for display
// 
The Solin Twiner plugin currently knows 4 different type of actions, notify, enrol, put in a group or put in a cohort. Each action has it's own class in the classes map. Each class has a function called "get_and_print_action_triggers" to display the overview of the triggers in the list. 