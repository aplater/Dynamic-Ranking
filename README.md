# Dynamic Ranking Using Google Charts

<img src='https://i.gyazo.com/bc67b70940348eb71f93aaae3c6d3302.png'></img>



With the query only

<img src="https://i.gyazo.com/a7d642452a688aac326a844d6594d492.png">

<code>Query => Select count(Name) as MembersTotal, G_Name as GuildName from GuildMember group by G_Name order by MembersTotal DEsc;</code>

Query => Create view Guilds as Select count(Name) as GuildMembersTotal, G_Name as GName from GuildMember group by G_Name;

Sorting example using Google Charts API. 

Very simple and usefull for any web/mobile aplication that needs dynamic searches, pagination and filters without the need to create your own logic that will save a lot of time.


