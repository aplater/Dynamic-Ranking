Select c.AccountID as Account, c.Name as CharName, c.Resets as CharResets, c.cLevel as CharLevel,
     (case
          when c.MapNumber  = 0 then 'Lorencia'
          when c.MapNumber  = 1 then 'Dungeon'
          when c.MapNumber  = 2 then 'Davias'
          when c.MapNumber  = 3 then 'Noria'
          when c.MapNumber  = 4 then 'Lost Tower'
          when c.MapNumber  = 6 then 'Arena'
          when c.MapNumber  = 7 then 'Atlans'
          when c.MapNumber  = 8 then 'Tarkan'
          when c.MapNumber  = 9 then 'Devil Square'
          when c.MapNumber  = 10 then 'Icarus'
          when c.MapNumber  between 11 and 17 then CONCAT('Blood castle ',c.MapNumber - 10)
          when c.MapNumber  between 18 and 23 then CONCAT('Chaos castle ',c.MapNumber - 17)
          when c.MapNumber = 25 then 'Kalima 2'
          when (c.MapNumber between 24 and 29) and (c.MapNumber != 25) then 'Kalima 1'
          when c.MapNumber = 30 and c.MapNumber = 55 then 'Valey of Loren'
          when c.MapNumber = 31 then 'Land of Trial'
          when c.MapNumber = 54 then 'Aida'
          when c.MapNumber = 33 then 'Aida 2'
          when c.MapNumber = 34 then 'CryWolf'
          else 'Unknown'
      end) as CharLocation,
     (case
          when c.Class = 0 then 'Dark Wizard'
          when c.Class = 1 then 'Soul Master'
          when c.Class between 2 and 3 then 'Grand Master'
          when c.Class = 16 then 'Dark Knight'
          when c.Class = 17 then 'Blade Knight'
          when c.Class between 18 and 19 then 'Blade Master'
          when c.Class = 32 then 'Fairy Elf'
          when c.Class = 33 then 'Muse Elf'
          when c.Class between 34 and 35 then 'High Elf'
          when c.Class = 48 then 'Magic Gladiator'
          when c.Class between 49 and 50 then 'Duel Master'
          when c.Class = 48 then 'Magic Gladiator'
          when c.Class = 64 then 'Dark Lord'
          when c.Class between 65 and 66 then 'Lord Emperor'
          when c.Class = 80 then 'Summoner'
          when c.Class = 81 then 'Bloody Summoner'
          when c.Class between 82and 83 then 'Dimension Master'
          else 'Unknown'
       end) as CharClass,
       (case
          when c.CtlCode = 8 or c.CtlCode = 32 then 'GM'
          when c.CtlCode = 0 then 'Normal'
          when c.CtlCode = 1 then 'Banned'
       end) as CharType,
       (case
          when c.PkLevel = 1 then 'Hero'
          when c.PkLevel = 3 then 'Normal'
          when c.PkLevel = 4 then 'Murderer'
          when c.PkLevel = 6 then 'Phonomania'
          else 'Normal'
       end) as PKStatus,
       (case
          when m.ConnectStat = 1 and c.Name = a.GameIDC then 'Online'
          else 'Offline'
       end)    as OnlineStatus,
       (case
          when cr.credits > 0 then cr.credits
          else 0
       end) as Credits,
       (case
          when gu.G_Name is NULL then 'No Guild'
          else gu.G_Name
       end) as GuildName,
       (case
          when gmb.GuildMembersTotal > 0 then gmb.GuildMembersTotal
          else 0
       end) as GuildMembersTotal
       from Character as c
       LEFT Join MEMB_STAT as m on m.memb___id = c.AccountID
       LEFT Join AccountCharacter as a on a.ID=m.memb___id
       LEFT Join MEMB_CREDITS as cr on cr.memb___id = m.memb___id collate Modern_Spanish_CI_AS
       LEFT Join GuildMember as gu on gu.Name = c.Name
       Left Join Guilds as gmb on gmb.GName = gu.G_Name
