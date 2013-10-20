//
//  PostListTableCell.m
//  ipapp
//
//  Created by kelaocai on 13-9-29.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "ThreadListTableCell.h"

@implementation ThreadListTableCell

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
        self.logo = [[[UIImageView alloc] initWithFrame:CGRectMake(5, 5, 36, 36)] autorelease];
        self.logo.backgroundColor = [UIColor clearColor];
        self.logo.layer.cornerRadius = 8;
        self.logo.layer.masksToBounds = YES;
        //自适应图片宽高比例
        self.logo.contentMode = UIViewContentModeScaleAspectFit;

        [self.contentView addSubview:self.logo];
        
        self.title = [[[UILabel alloc] initWithFrame:CGRectMake(60, 10, 230, 20)] autorelease];
        self.title.font = [UIFont systemFontOfSize:16.0f];
        self.title.backgroundColor = [UIColor clearColor];
        self.title.opaque = NO;
        [self.contentView addSubview:self.title];
        //最后回复者
        self.lastReplyAvtar = [[[UIImageView alloc] initWithFrame:CGRectMake(70, 32, 18, 18)] autorelease];
        self.lastReplyAvtar.backgroundColor = [UIColor clearColor];
        self.lastReplyAvtar.layer.cornerRadius = 4;
        self.lastReplyAvtar.layer.masksToBounds = YES;
        self.lastReplyAvtar.contentMode = UIViewContentModeScaleAspectFit;
        
        [self.contentView addSubview:self.lastReplyAvtar];
        
        //概要字体颜色
        UIColor *subFontColor=[UIColor colorWithRed:158/255.0
                                              green:158/255.0
                                               blue:158/255.0
                                              alpha:1.0];

        //最后回复message
        self.lastPostMessage = [[[UILabel alloc] initWithFrame:CGRectMake(96, 32, 180, 18)] autorelease];
        self.lastPostMessage.font = [UIFont systemFontOfSize:12.0f];
        self.lastPostMessage.textColor = subFontColor;
        self.lastPostMessage.backgroundColor = [UIColor clearColor];
        [self.contentView addSubview:self.lastPostMessage];


        
        self.author = [[[UILabel alloc] initWithFrame:CGRectMake(60, 60, 80, 16)] autorelease];
        self.author.font = [UIFont systemFontOfSize:12.0f];
        self.author.textColor = subFontColor;
        self.author.backgroundColor = [UIColor clearColor];
        self.author.opaque = NO;
        [self.contentView addSubview:self.author];
      
//        UIImageView *icon_clock_view = [[[UIImageView alloc] initWithFrame:CGRectMake(170, 40, 16, 16)] autorelease];
//        icon_clock_view.image=[UIImage imageNamed:@"icon_clock.png"];
//        [self.contentView addSubview:icon_clock_view];
        
        self.time = [[[UILabel alloc] initWithFrame:CGRectMake(170, 60, 60, 16)] autorelease];
        self.time.font = [UIFont systemFontOfSize:11.0f];
        self.time.textColor = [UIColor colorWithRed:158/255.0
                                                  green:158/255.0
                                                   blue:158/255.0
                                                  alpha:1.0];
        self.time.backgroundColor = [UIColor clearColor];
        self.time.opaque = NO;
        [self.contentView addSubview:self.time];

        
        UIImageView *icon_comment_view = [[[UIImageView alloc] initWithFrame:CGRectMake(280, 60, 16, 16)] autorelease];
        icon_comment_view.image=[UIImage imageNamed:@"icon_chat.png"];
        [self.contentView addSubview:icon_comment_view];
        
        self.comment = [[[UILabel alloc] initWithFrame:CGRectMake(300, 60, 60, 16)] autorelease];
        self.comment.font = [UIFont systemFontOfSize:11.0f];
        self.comment.textColor = [UIColor colorWithRed:158/255.0
                                              green:158/255.0
                                               blue:158/255.0
                                              alpha:1.0];
        self.comment.backgroundColor = [UIColor clearColor];
        self.comment.opaque = NO;
        [self.contentView addSubview:self.comment];
        
        UILabel *sLine1 = [[[UILabel alloc] initWithFrame:CGRectMake(46, 0, 1, 80)] autorelease];
        sLine1.backgroundColor = [UIColor colorWithRed:239/255.0
                                                 green:239/255.0
                                                  blue:239/255.0
                                                 alpha:0.5];
        [self.contentView addSubview:sLine1];




        //cell分割线
        /*
        UILabel *sLine1 = [[[UILabel alloc] initWithFrame:CGRectMake(0, 78, 320, 1)] autorelease];
        sLine1.backgroundColor = [UIColor colorWithRed:198/255.0
                                                 green:198/255.0
                                                  blue:198/255.0
                                                 alpha:1.0];
        UILabel *sLine2 = [[[UILabel alloc] initWithFrame:CGRectMake(0, 79, 320, 1)] autorelease];
        sLine2.backgroundColor = [UIColor whiteColor];
        
        [self.contentView addSubview:sLine1];
        [self.contentView addSubview:sLine2];
         */
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
