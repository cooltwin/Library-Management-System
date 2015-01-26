# Library-Management-System
Library Management System allows users to easily search for a book, checkout a book, check-in a book, Add new borrowers to the system and computes fine for the borrowed books. Above all it authenticates the librarian to the library database system to prevent any kind of misuse of the system.

Following is a brief description about the working of the system:-

- Homepage - Authenticates the librarian, if failed no activity can be performed on the system.

- Search Book - Allows the user to search any book given any combination of Book id and/or Book Title and /or Book Author. This provides flexibility to the librarians in searching the book in any order.

- Checkout Book – Allows a user to check-out the book from a branch based on its availability and book borrowers credibility. Checkout is restricted for all borrowers who have exceeded 3 book loans or have any past fines due on them.

- Checkin Book - Allows a user to check-in the book. This feature first searches for all the book loans the borrower has taken which are not checked-in yet and then allows the user to check in the selected the book. It also intimates the user if the borrower has any fine due on that book.

- Add New Borrower - Allows the user to add a new borrower to the library loan system. The system generates a unique card no for each borrower. Uniqueness is defined based on the first name, last name and address details of a borrower.

- Manage Fine - This feature allows two types of fine computation. First – Computes the fine for all the borrowers who have taken a book loan. Second – Computes the fine for an individual book borrower who has taken a book loan.

Design Decisions and Justifications:-

I have added an additional feature in  Library Management System to authenticate the users, making it secure for any database break-in attacks. This feature authenticates user’s based on their database credentials. 

Also while checking in a book, if no of books borrowed is more than 3(i.e. no of book loans >3) then the system rejects the checkin request. As this exceeds the maximum capacity of book loans permitted to a borrower.

On the more I thought it’s good to intimate a user about the fine he/she has incurred while the borrower is trying to check-in the book. So if the user is doing a check-in after the due date, he/she is intimated about the fine, and if willing then the borrower can pay the fine after the book is checked in or pay later.

Above all a user can search for individual book borrower based on their card no and /or cardholder’s name. Note: Fine is computed $0.25 per day starting from the due date till the date the book is checked in.


Technical Dependencies:- 
* Source Code :- PHP v 5.3 
* Database :- Mysql v5.6.21
* Operating System :- Windows/Mac/Linux 
* Browser to run the webpage:- Google Chrome / Mozilla Firefox
